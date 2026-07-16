<?php

declare(strict_types=1);

namespace App\Controller;

use App\Middleware\RateLimitMiddleware;
use App\Service\PasswordResetService;

class PasswordRecoveryController extends AppController
{
    private PasswordResetService $_passwordResetService;

    public function initialize(): void
    {
        parent::initialize();
        $this->_passwordResetService = new PasswordResetService();
    }

    /**
     * Tela pública de "esqueci minha senha" — pede o e-mail e envia um link
     * de redefinição por e-mail. Sempre mostra a mesma mensagem de sucesso,
     * exista ou não conta com esse e-mail, para não revelar cadastros.
     */
    public function request()
    {
        if ($this->request->is('post')) {
            $clientIp = $this->getClientIp();
            $rateLimiter = new RateLimitMiddleware(5, 300, 'rate_limit', 'password_recovery');

            if ($rateLimiter->getRemainingAttempts($clientIp) <= 0) {
                $this->Flash->error(__('Muitas tentativas. Tente novamente em alguns minutos.'));
                return;
            }

            $this->recordFailedAttempt($clientIp, 'password_recovery');

            $email = strtolower(trim((string)$this->request->getData('email')));

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->_passwordResetService->sendResetLinkIfAccountExists($email);
            }

            $this->Flash->success(__(
                'Se este e-mail estiver cadastrado, enviamos um link de redefinição de senha para ele.'
            ));

            return $this->redirect(['action' => 'request']);
        }
    }

    /**
     * Tela pública de redefinição — acessada pelo link do e-mail.
     */
    public function reset($rawToken = null)
    {
        $token = $this->_passwordResetService->findValidToken($rawToken);

        if (!$token) {
            $this->Flash->error(__('Link de redefinição inválido ou expirado. Solicite um novo.'));
            return $this->redirect(['action' => 'request']);
        }

        if ($this->request->is('post')) {
            $clientIp = $this->getClientIp();
            $this->recordFailedAttempt($clientIp, 'password_recovery');

            $password = (string)$this->request->getData('password');
            $confirmPassword = (string)$this->request->getData('confirm_password');

            if (strlen($password) < 6) {
                $this->Flash->error(__('A senha deve ter pelo menos 6 caracteres.'));
            } elseif ($password !== $confirmPassword) {
                $this->Flash->error(__('Confirmação de senha não confere.'));
            } else {
                $this->_passwordResetService->applyNewPassword($token, $password);

                $this->Flash->success(__('Senha redefinida com sucesso. Faça login com sua nova senha.'));

                return $this->redirect('/login');
            }
        }

        $this->set(['rawToken' => $rawToken]);
    }
}
