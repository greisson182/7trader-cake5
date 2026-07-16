<?php

declare(strict_types=1);

namespace App\Controller;

use App\Middleware\RateLimitMiddleware;
use App\Service\Exception\AccountRequiresLoginException;
use App\Service\MercadoPagoService;
use App\Service\OrderService;
use App\Service\PasswordResetService;
use App\Service\SettingsService;
use Cake\Cache\Cache;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Routing\Router;
use Exception;

class CheckoutController extends AppController
{
    private OrderService $_orderService;
    private MercadoPagoService $_mercadoPagoService;
    private SettingsService $_settingsService;
    private PasswordResetService $_passwordResetService;

    public function initialize(): void
    {
        parent::initialize();
        $this->_settingsService = new SettingsService();
        $this->_orderService = new OrderService();
        $this->_mercadoPagoService = new MercadoPagoService($this->_settingsService);
        $this->_passwordResetService = new PasswordResetService();
    }

    public function course($courseId = null)
    {
        return $this->startCheckout('course', (int)$courseId);
    }

    public function mentorship($mentorshipId = null)
    {
        return $this->startCheckout('mentorship', (int)$mentorshipId);
    }

    private function startCheckout(string $itemType, int $itemId)
    {
        try {
            $item = $this->_orderService->getPricedItem($itemType, $itemId);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error($e->getMessage());
            return $this->redirect('/');
        }

        $loggedUser = $this->getLoggedUser();
        // "Já tem conta? Faça login" (link proativo, antes de qualquer erro)
        // cai no mesmo card de login embutido usado quando o e-mail já tem
        // conta — nunca navega pra fora do checkout.
        $requiresLogin = !$loggedUser && $this->request->getQuery('login') !== null;
        // "Esqueceu a senha?" é uma tela própria dentro do checkout (só e-mail
        // + botão), separada do formulário de login — evita misturar a
        // validação dos dois formulários num só.
        $showForgotPasswordForm = !$loggedUser && $this->request->getQuery('forgot') !== null;

        if ($this->request->is('post')) {
            $clientIp = $this->getClientIp();
            // Limite mais generoso que os outros fluxos: esta tela reúne 3 ações
            // (login, "esqueci a senha" e cadastro/pagamento) compartilhando o
            // mesmo contador de tentativas — 5 em 5min esgotaria rápido demais
            // num vaivém legítimo entre elas.
            $rateLimiter = new RateLimitMiddleware(15, 600, 'rate_limit', 'checkout');

            if ($rateLimiter->getRemainingAttempts($clientIp) <= 0) {
                $this->Flash->error(__('Muitas tentativas. Tente novamente em alguns minutos.'));
                $this->set(compact('item', 'itemType', 'itemId', 'loggedUser', 'requiresLogin', 'showForgotPasswordForm'));
                return $this->render('start');
            }

            // Login embutido no próprio checkout: quando o e-mail já tem conta,
            // autentica aqui mesmo (sem sair para a tela de login do site) e
            // mantém o comprador no fluxo, caindo direto na tela de confirmação
            // de compra do usuário logado.
            if (!$loggedUser && $this->request->getData('action_type') === 'login') {
                if ($this->attemptInlineLogin($this->request->getData(), $clientIp)) {
                    $loggedUser = $this->getLoggedUser();
                } else {
                    $this->recordFailedAttempt($clientIp, 'checkout');
                    $this->Flash->error(__('E-mail ou senha inválidos.'));
                    $requiresLogin = true;
                }

                $formData = $this->request->getData();
                $this->set(compact('item', 'itemType', 'itemId', 'formData', 'loggedUser', 'requiresLogin', 'showForgotPasswordForm'));
                return $this->render('start');
            }

            // "Esqueci a senha": envia o e-mail de redefinição sem sair da
            // compra. Nunca revela se o e-mail existe ou não. Fica na mesma
            // tela de "recuperar acesso" mostrando a confirmação de envio.
            if (!$loggedUser && $this->request->getData('action_type') === 'forgot_password') {
                $this->recordFailedAttempt($clientIp, 'checkout');

                $email = strtolower(trim((string)$this->request->getData('login_email')));

                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->_passwordResetService->sendResetLinkIfAccountExists($email);
                }

                $this->Flash->success(__(
                    'Se este e-mail estiver cadastrado, enviamos um link de redefinição de senha para ele.'
                ));

                $showForgotPasswordForm = true;
                $requiresLogin = false;
                $formData = $this->request->getData();
                $this->set(compact('item', 'itemType', 'itemId', 'formData', 'loggedUser', 'requiresLogin', 'showForgotPasswordForm'));
                return $this->render('start');
            }

            try {
                $order = $loggedUser
                    ? $this->_orderService->createPendingOrderForLoggedUser($itemType, $itemId, $loggedUser->id, $clientIp)
                    : $this->_orderService->createPendingOrder($itemType, $itemId, $this->request->getData(), $clientIp);

                $preference = $this->_mercadoPagoService->createPreference(
                    $order->id,
                    $item['title'],
                    (float)$order->amount,
                    $order->buyer_name,
                    $order->buyer_email,
                    Router::url(['controller' => 'Checkout', 'action' => 'returnSuccess'], true),
                    Router::url(['controller' => 'Checkout', 'action' => 'returnPending'], true),
                    Router::url(['controller' => 'Checkout', 'action' => 'returnFailure'], true),
                    Router::url(['prefix' => 'Api', 'controller' => 'MercadoPagoWebhook', 'action' => 'notify'], true)
                );

                $this->_orderService->attachPreference($order, $preference->id);

                $settings = $this->_settingsService->get();
                $redirectUrl = !empty($settings->mp_sandbox)
                    ? $preference->sandbox_init_point
                    : $preference->init_point;

                return $this->redirect($redirectUrl);
            } catch (AccountRequiresLoginException $e) {
                $this->Flash->error($e->getMessage());
                $requiresLogin = true;
            } catch (Exception $e) {
                $this->recordFailedAttempt($clientIp, 'checkout');
                $this->Flash->error(__('Erro ao iniciar pagamento: ') . $e->getMessage());
            }
        }

        $formData = $this->request->getData();
        $this->set(compact('item', 'itemType', 'itemId', 'formData', 'loggedUser', 'requiresLogin', 'showForgotPasswordForm'));
        return $this->render('start');
    }

    private function getLoggedUser(): ?object
    {
        $session = $this->request->getSession();

        return $session->check('logado') ? $session->read('logado') : null;
    }

    /**
     * Autentica o comprador diretamente na página de checkout (mesma lógica de
     * credencial do login padrão do site), evitando redirecioná-lo para fora
     * do fluxo de compra. Grava a sessão 'logado' no mesmo formato usado pelo
     * resto da aplicação.
     */
    private function attemptInlineLogin(array $data, string $clientIp): bool
    {
        $identifier = trim((string)($data['login_email'] ?? ''));
        $password = (string)($data['login_password'] ?? '');

        if ($identifier === '' || $password === '') {
            return false;
        }

        $usersTable = $this->fetchTable('Users');
        $user = $usersTable->find()
            ->contain(['Groupps', 'Profiles'])
            ->where([
                'or' => [
                    'username' => $identifier,
                    'email' => $identifier,
                ],
            ])
            ->first();

        if (!$user || $user->blocked || in_array($user->status, ['i', 'd'], true)) {
            return false;
        }

        if (empty($user->password) || !password_verify($password, $user->password)) {
            return false;
        }

        $key = RateLimitMiddleware::buildCacheKey($clientIp, 'checkout');
        Cache::delete($key, 'rate_limit');

        $session = $this->request->getSession();
        $user->last_login = date('Y-m-d H:i:s');
        $user->session_id = $session->id();
        $usersTable->save($user);

        $fullUser = $usersTable->find()
            ->contain(['Groupps', 'Profiles', 'Students'])
            ->where(['Users.id' => $user->id])
            ->first();

        $session->write('logado', $fullUser);

        return true;
    }

    public function returnSuccess()
    {
        return $this->showReturnPage('success');
    }

    public function returnPending()
    {
        return $this->showReturnPage('pending');
    }

    public function returnFailure()
    {
        return $this->showReturnPage('failure');
    }

    private function showReturnPage(string $outcome)
    {
        $externalReference = $this->request->getQuery('external_reference');
        $order = null;

        if ($externalReference !== null && ctype_digit((string)$externalReference)) {
            try {
                $order = $this->_orderService->findOrderById((int)$externalReference);
            } catch (RecordNotFoundException $e) {
                $order = null;
            }
        }

        $this->set(compact('order', 'outcome'));

        return $this->render('return');
    }
}
