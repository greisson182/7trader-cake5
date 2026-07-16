<?php

declare(strict_types=1);

namespace App\Service;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class PasswordResetService
{
    private const TOKEN_TTL_SECONDS = 3600;

    private Table $_usersTable;
    private Table $_tokensTable;
    private AwsEmailService $_awsEmailService;

    public function __construct(?AwsEmailService $awsEmailService = null)
    {
        $this->_usersTable = TableRegistry::getTableLocator()->get('Users');
        $this->_tokensTable = TableRegistry::getTableLocator()->get('PasswordResetTokens');
        $this->_awsEmailService = $awsEmailService ?? new AwsEmailService();
    }

    /**
     * Sempre "silencioso": não revela se o e-mail existe ou não. Quem chama
     * deve mostrar a mesma mensagem de sucesso independente do retorno.
     */
    public function sendResetLinkIfAccountExists(string $email): void
    {
        $user = $this->_usersTable->find()->where(['email' => $email])->first();

        if (!$user || $user->blocked || in_array($user->status, ['i', 'd'], true)) {
            return;
        }

        // Invalida qualquer link de redefinição anterior ainda não usado.
        $this->_tokensTable->deleteAll(['user_id' => $user->id, 'used_at IS' => null]);

        $rawToken = bin2hex(random_bytes(32));

        $token = $this->_tokensTable->newEmptyEntity();
        $token = $this->_tokensTable->patchEntity($token, [
            'user_id' => $user->id,
            // Só o hash fica salvo no banco — o token em texto puro só existe no link do e-mail.
            'token_hash' => hash('sha256', $rawToken),
            'expires_at' => date('Y-m-d H:i:s', time() + self::TOKEN_TTL_SECONDS),
        ]);

        if (!$this->_tokensTable->save($token)) {
            return;
        }

        $resetUrl = Router::url(['controller' => 'PasswordRecovery', 'action' => 'reset', $rawToken], true);

        $this->sendResetEmail($user, $resetUrl);
    }

    /**
     * Envio melhor-esforço via Settings.aws_api — se não estiver configurado,
     * o envio é pulado silenciosamente, sem travar o fluxo.
     */
    private function sendResetEmail(object $user, string $resetUrl): void
    {
        $safeName = h($user->name);
        $safeUrl = h($resetUrl);

        $html = <<<HTML
            <div style="font-family: Arial, sans-serif; max-width: 480px; margin: 0 auto; color: #222;">
                <h2>Olá, {$safeName}!</h2>
                <p>Recebemos um pedido para redefinir sua senha.</p>
                <p>
                    <a href="{$safeUrl}" style="background:#00ff88;color:#000;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:bold;display:inline-block;">
                        Redefinir minha senha
                    </a>
                </p>
                <p>Ou copie e cole este link no navegador:<br>{$safeUrl}</p>
                <p>Este link expira em 1 hora. Se você não pediu isso, pode ignorar este e-mail.</p>
                <p>Equipe 7Trader</p>
            </div>
            HTML;

        $this->_awsEmailService->send($user->email, 'Redefinição de senha - 7Trader', $html);
    }

    public function findValidToken(?string $rawToken): ?object
    {
        if (!$rawToken) {
            return null;
        }

        return $this->_tokensTable->find()
            ->where([
                'token_hash' => hash('sha256', $rawToken),
                'used_at IS' => null,
                'expires_at >' => date('Y-m-d H:i:s'),
            ])
            ->first();
    }

    public function applyNewPassword(object $token, string $password): void
    {
        $usersTable = $this->_usersTable;
        $tokensTable = $this->_tokensTable;

        $connection = $usersTable->getConnection();
        $connection->transactional(function () use ($usersTable, $tokensTable, $token, $password) {
            $user = $usersTable->get($token->user_id);
            $user = $usersTable->patchEntity($user, ['password' => password_hash($password, PASSWORD_DEFAULT)]);
            $usersTable->save($user);

            $token = $tokensTable->patchEntity($token, ['used_at' => date('Y-m-d H:i:s')]);
            $tokensTable->save($token);
        });
    }
}
