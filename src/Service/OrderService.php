<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Order;
use App\Service\Exception\AccountRequiresLoginException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Mailer\Mailer;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Exception;
use Throwable;

class OrderService
{
    private Table $_ordersTable;
    private Table $_coursesTable;
    private Table $_mentorshipsTable;
    private Table $_usersTable;
    private Table $_courseEnrollmentsTable;
    private StudentService $_studentService;

    public function __construct(?StudentService $studentService = null)
    {
        $this->_ordersTable = TableRegistry::getTableLocator()->get('Orders');
        $this->_coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $this->_mentorshipsTable = TableRegistry::getTableLocator()->get('Mentorships');
        $this->_usersTable = TableRegistry::getTableLocator()->get('Users');
        $this->_courseEnrollmentsTable = TableRegistry::getTableLocator()->get('CourseEnrollments');
        $this->_studentService = $studentService ?? new StudentService();
    }

    /**
     * Carrega o item vendável (curso ou mentoria) direto do banco — o preço
     * cobrado é sempre este, nunca um valor vindo do formulário de checkout.
     *
     * @return array{title: string, price: float}
     */
    public function getPricedItem(string $itemType, int $itemId): array
    {
        if ($itemType === 'course') {
            $course = $this->_coursesTable->find()
                ->where(['id' => $itemId, 'is_active' => 1])
                ->first();

            if (!$course) {
                throw new RecordNotFoundException(__('Curso não encontrado.'));
            }

            return ['title' => $course->title, 'price' => (float)$course->price];
        }

        if ($itemType === 'mentorship') {
            $mentorship = $this->_mentorshipsTable->find()
                ->where(['id' => $itemId, 'is_active' => 1])
                ->first();

            if (!$mentorship) {
                throw new RecordNotFoundException(__('Mentoria não encontrada.'));
            }

            return ['title' => $mentorship->name, 'price' => (float)$mentorship->price];
        }

        throw new Exception(__('Tipo de produto inválido.'));
    }

    public function createPendingOrder(string $itemType, int $itemId, array $buyerData, string $clientIp): Order
    {
        $item = $this->getPricedItem($itemType, $itemId);

        $name = trim(strip_tags((string)($buyerData['name'] ?? '')));
        $email = strtolower(trim((string)($buyerData['email'] ?? '')));
        $phone = trim(strip_tags((string)($buyerData['phone'] ?? '')));
        $password = (string)($buyerData['password'] ?? '');
        $confirmPassword = (string)($buyerData['confirm_password'] ?? '');

        if (strlen($name) < 2 || strlen($name) > 255) {
            throw new Exception(__('Nome inválido.'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception(__('E-mail inválido.'));
        }

        if (strlen($password) < 6) {
            throw new Exception(__('A senha deve ter pelo menos 6 caracteres.'));
        }

        if ($password !== $confirmPassword) {
            throw new Exception(__('Confirmação de senha não confere.'));
        }

        if ($phone !== '' && strlen($phone) > 20) {
            throw new Exception(__('Telefone inválido.'));
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $connection = $this->_ordersTable->getConnection();

        return $connection->transactional(function () use (
            $itemType,
            $itemId,
            $name,
            $email,
            $phone,
            $passwordHash,
            $item,
            $clientIp
        ) {
            [$studentId, $userId] = $this->resolveOrCreateAccount($name, $email, $passwordHash);

            $order = $this->_ordersTable->newEmptyEntity();
            $order = $this->_ordersTable->patchEntity($order, [
                'item_type' => $itemType,
                'item_id' => $itemId,
                'buyer_name' => $name,
                'buyer_email' => $email,
                'buyer_phone' => $phone !== '' ? $phone : null,
                'buyer_password_hash' => $passwordHash,
                'amount' => $item['price'],
                'status' => 'pending',
                'ip_address' => $clientIp,
                'student_id' => $studentId,
                'user_id' => $userId,
            ]);

            if ($order->hasErrors() || !$this->_ordersTable->save($order)) {
                throw new Exception(__('Erro ao criar pedido. Verifique os dados informados.'));
            }

            return $order;
        });
    }

    /**
     * Cria o pedido para um comprador que já está logado — pula formulário
     * de cadastro/senha por completo, usa a identidade da sessão.
     */
    public function createPendingOrderForLoggedUser(string $itemType, int $itemId, int $userId, string $clientIp): Order
    {
        $item = $this->getPricedItem($itemType, $itemId);
        $user = $this->_usersTable->get($userId);

        $order = $this->_ordersTable->newEmptyEntity();
        $order = $this->_ordersTable->patchEntity($order, [
            'item_type' => $itemType,
            'item_id' => $itemId,
            'buyer_name' => $user->name,
            'buyer_email' => $user->email,
            'buyer_phone' => null,
            'buyer_password_hash' => $user->password,
            'amount' => $item['price'],
            'status' => 'pending',
            'ip_address' => $clientIp,
            'student_id' => $user->student_id,
            'user_id' => $user->id,
        ]);

        if ($order->hasErrors() || !$this->_ordersTable->save($order)) {
            throw new Exception(__('Erro ao criar pedido.'));
        }

        return $order;
    }

    /**
     * Garante que o cadastro do comprador (Student + User) já existe no envio
     * do formulário de checkout, antes de ir pro Mercado Pago — não espera a
     * confirmação do pagamento. Conta nova nasce com `status = 'i'` (não
     * consegue logar) até o webhook confirmar o pagamento; se o e-mail já tem
     * conta, reaproveita e não mexe em senha nem status de conta já ativa.
     */
    private function resolveOrCreateAccount(string $name, string $email, string $passwordHash): array
    {
        $existingUser = $this->_usersTable->find()
            ->where(['email' => $email])
            ->first();

        if ($existingUser) {
            if ($existingUser->status !== 'i') {
                throw new AccountRequiresLoginException(
                    __('Este e-mail já possui uma conta. Faça login antes de continuar a compra.')
                );
            }

            $existingUser = $this->_usersTable->patchEntity($existingUser, ['password' => $passwordHash]);
            $this->_usersTable->save($existingUser);

            return [$existingUser->student_id, $existingUser->id];
        }

        $result = $this->_studentService->create([
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        // active=0 além de status='i': mantém a conta pendente fora das
        // listagens/contadores de alunos do admin até o pagamento confirmar.
        $newUser = $this->_usersTable->patchEntity($result['user'], ['status' => 'i', 'active' => 0]);
        $this->_usersTable->save($newUser);

        return [$result['student']->id, $newUser->id];
    }

    public function attachPreference(Order $order, string $preferenceId): void
    {
        $order = $this->_ordersTable->patchEntity($order, ['mp_preference_id' => $preferenceId]);
        if (!$this->_ordersTable->save($order)) {
            throw new Exception(__('Erro ao vincular preferência de pagamento ao pedido.'));
        }
    }

    public function findOrderById(int $id): Order
    {
        $order = $this->_ordersTable->find()->where(['id' => $id])->first();

        if (!$order) {
            throw new RecordNotFoundException(__('Pedido não encontrado.'));
        }

        return $order;
    }

    /**
     * Processa a confirmação de pagamento vinda do webhook. Idempotente:
     * notificações duplicadas do Mercado Pago para o mesmo pagamento/status
     * não reprocessam o pedido.
     */
    public function processPaymentUpdate(object $mpPayment): void
    {
        $externalReference = $mpPayment->external_reference ?? null;
        if (!$externalReference || !ctype_digit((string)$externalReference)) {
            return;
        }

        $connection = $this->_ordersTable->getConnection();

        $connection->transactional(function () use ($mpPayment, $externalReference) {
            $order = $this->_ordersTable->find()
                ->where(['id' => (int)$externalReference])
                ->epilog('FOR UPDATE')
                ->first();

            if (!$order) {
                return;
            }

            $newStatus = $this->mapMercadoPagoStatus((string)$mpPayment->status);
            $paymentId = (string)$mpPayment->id;

            if ($order->mp_payment_id === $paymentId && $order->status === $newStatus) {
                return;
            }

            $wasAlreadyFulfilled = $order->fulfilled_at !== null;

            $order = $this->_ordersTable->patchEntity($order, [
                'status' => $newStatus,
                'mp_payment_id' => $paymentId,
                'payment_method' => $mpPayment->payment_method_id ?? null,
            ]);
            $this->_ordersTable->save($order);

            if ($newStatus === 'approved' && !$wasAlreadyFulfilled) {
                $this->fulfillOrder($order);
            }
        });
    }

    private function mapMercadoPagoStatus(string $mpStatus): string
    {
        return match ($mpStatus) {
            'approved' => 'approved',
            'rejected' => 'rejected',
            'cancelled' => 'cancelled',
            'refunded', 'charged_back' => 'refunded',
            default => 'pending',
        };
    }

    private function fulfillOrder(Order $order): void
    {
        $userId = $order->user_id;
        $studentId = $order->student_id;

        // Pedidos criados antes do cadastro passar a ser feito no checkout
        // (student_id/user_id ainda nulos) caem aqui como rede de segurança.
        if (!$userId) {
            $existingUser = $this->_usersTable->find()
                ->where(['email' => $order->buyer_email])
                ->first();

            if ($existingUser) {
                $studentId = $existingUser->student_id;
                $userId = $existingUser->id;
            } else {
                $result = $this->_studentService->create([
                    'name' => $order->buyer_name,
                    'email' => $order->buyer_email,
                    'password_hash' => $order->buyer_password_hash,
                ]);
                $studentId = $result['student']->id;
                $userId = $result['user']->id;
            }
        }

        $user = $this->_usersTable->get($userId);
        if ($user->status === 'i') {
            $user = $this->_usersTable->patchEntity($user, ['status' => 'a', 'active' => 1]);
            $this->_usersTable->save($user);
        }

        $order = $this->_ordersTable->patchEntity($order, [
            'student_id' => $studentId,
            'user_id' => $userId,
            'fulfilled_at' => date('Y-m-d H:i:s'),
        ]);
        $this->_ordersTable->save($order);

        if ($order->item_type === 'course' && $studentId) {
            $existingEnrollment = $this->_courseEnrollmentsTable->find()
                ->where(['student_id' => $studentId, 'course_id' => $order->item_id])
                ->first();

            if (!$existingEnrollment) {
                $enrollment = $this->_courseEnrollmentsTable->newEmptyEntity();
                $enrollment = $this->_courseEnrollmentsTable->patchEntity($enrollment, [
                    'student_id' => $studentId,
                    'course_id' => $order->item_id,
                    'enrolled_at' => date('Y-m-d H:i:s'),
                ]);
                $this->_courseEnrollmentsTable->save($enrollment);
            }
        }

        // Mentorias (grupo ou particular) não têm entrega automática de conteúdo hoje:
        // o pedido aprovado fica visível na tela de Orders do admin para acompanhamento manual.

        $this->sendAccessEmail($order);
    }

    /**
     * Envio melhor-esforço: o EmailTransport do projeto ainda não tem SMTP
     * real configurado, então uma falha aqui nunca deve desfazer o pedido.
     */
    private function sendAccessEmail(Order $order): void
    {
        try {
            $loginUrl = Router::url(['prefix' => 'Admin', 'controller' => 'Users', 'action' => 'login'], true);

            (new Mailer('default'))
                ->setTo($order->buyer_email)
                ->setSubject('Seu acesso foi liberado - 7Trader')
                ->deliver(
                    "Olá, {$order->buyer_name}!\n\n" .
                    "Seu pagamento foi confirmado e seu acesso já está liberado.\n" .
                    "Faça login em {$loginUrl} com o e-mail {$order->buyer_email}.\n\n" .
                    "Equipe 7Trader"
                );
        } catch (Throwable $e) {
            // Best-effort: não bloqueia nem desfaz a confirmação do pedido.
        }
    }
}
