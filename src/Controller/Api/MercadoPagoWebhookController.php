<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\MercadoPagoService;
use App\Service\OrderService;
use App\Service\SettingsService;
use Throwable;

class MercadoPagoWebhookController extends AppController
{
    private MercadoPagoService $_mercadoPagoService;
    private OrderService $_orderService;

    public function initialize(): void
    {
        parent::initialize();
        $settingsService = new SettingsService();
        $this->_mercadoPagoService = new MercadoPagoService($settingsService);
        $this->_orderService = new OrderService();
    }

    /**
     * Endpoint de notificação do Mercado Pago. Isento de CSRF (ver
     * skipCheckCallback em src/Application.php para o prefixo Api).
     *
     * Nunca confia no corpo da notificação: valida a assinatura e sempre
     * busca o pagamento de verdade na API do Mercado Pago antes de aplicar
     * qualquer mudança de status.
     */
    public function notify()
    {
        $this->autoRender = false;

        if (!$this->request->is(['post', 'get'])) {
            return $this->response->withStatus(405);
        }

        $type = $this->request->getQuery('type') ?? $this->request->getQuery('topic');
        if ($type !== 'payment') {
            return $this->response->withStatus(200);
        }

        if (!$this->_mercadoPagoService->verifyWebhookSignature($this->request)) {
            return $this->response->withStatus(401);
        }

        $paymentId = $this->request->getQuery('data.id')
            ?? $this->request->getQuery('data_id')
            ?? $this->request->getQuery('id');

        if (!$paymentId) {
            return $this->response->withStatus(200);
        }

        try {
            $payment = $this->_mercadoPagoService->getPayment((string)$paymentId);
            $this->_orderService->processPaymentUpdate($payment);

            return $this->response->withStatus(200);
        } catch (Throwable $e) {
            // Erro 500 faz o Mercado Pago tentar reenviar a notificação depois.
            return $this->response->withStatus(500);
        }
    }
}
