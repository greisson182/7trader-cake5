<?php

declare(strict_types=1);

namespace App\Service;

use Cake\Http\Client;
use Cake\Http\ServerRequest;

class MercadoPagoService
{
    private const API_BASE_URL = 'https://api.mercadopago.com';

    private SettingsService $_settingsService;
    private Client $_client;

    public function __construct(?SettingsService $settingsService = null)
    {
        $this->_settingsService = $settingsService ?? new SettingsService();
        $this->_client = new Client();
    }

    /**
     * Cria uma preferência de pagamento (Checkout Pro) e retorna o objeto de resposta
     * do Mercado Pago (contém id, init_point, sandbox_init_point).
     */
    public function createPreference(
        int $orderId,
        string $itemTitle,
        float $amount,
        string $buyerName,
        string $buyerEmail,
        string $successUrl,
        string $pendingUrl,
        string $failureUrl,
        string $notificationUrl
    ): object {
        $settings = $this->_settingsService->get();

        $payload = [
            'items' => [[
                'title' => $itemTitle,
                'quantity' => 1,
                'unit_price' => $amount,
                'currency_id' => 'BRL',
            ]],
            'payer' => [
                'name' => $buyerName,
                'email' => $buyerEmail,
            ],
            'external_reference' => (string)$orderId,
            'back_urls' => [
                'success' => $successUrl,
                'pending' => $pendingUrl,
                'failure' => $failureUrl,
            ],
            'auto_return' => 'approved',
            'notification_url' => $notificationUrl,
            'statement_descriptor' => '7TRADER',
        ];

        $response = $this->_client->post(
            self::API_BASE_URL . '/checkout/preferences',
            $payload,
            [
                'type' => 'json',
                'headers' => [
                    'Authorization' => 'Bearer ' . $settings->mp_access_token,
                ],
            ]
        );

        if (!$response->isOk()) {
            throw new \RuntimeException(
                'Erro ao criar preferência no Mercado Pago: ' . $response->getStringBody()
            );
        }

        return json_decode($response->getStringBody());
    }

    /**
     * Busca um pagamento pelo id diretamente na API do Mercado Pago.
     * Única fonte confiável de status/valor de um pagamento — nunca confiar
     * no corpo da notificação do webhook.
     */
    public function getPayment(string $paymentId): object
    {
        $settings = $this->_settingsService->get();

        $response = $this->_client->get(
            self::API_BASE_URL . '/v1/payments/' . rawurlencode($paymentId),
            [],
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $settings->mp_access_token,
                ],
            ]
        );

        if (!$response->isOk()) {
            throw new \RuntimeException(
                'Erro ao buscar pagamento no Mercado Pago: ' . $response->getStringBody()
            );
        }

        return json_decode($response->getStringBody());
    }

    /**
     * Verifica a assinatura HMAC-SHA256 do webhook do Mercado Pago.
     *
     * Formato oficial do manifesto (confirmado na documentação do MP):
     *   id:{data.id};request-id:{x-request-id};ts:{ts};
     * (partes "id" e "request-id" são omitidas se ausentes; "ts" é sempre incluído)
     *
     * @see https://www.mercadopago.com.br/developers/en/docs/your-integrations/notifications/webhooks
     */
    public function verifyWebhookSignature(ServerRequest $request): bool
    {
        $settings = $this->_settingsService->get();
        $secret = $settings->mp_webhook_secret ?? '';

        if ($secret === '') {
            return false;
        }

        $signatureHeader = $request->getHeaderLine('x-signature');
        $requestIdHeader = $request->getHeaderLine('x-request-id');

        if ($signatureHeader === '') {
            return false;
        }

        $ts = null;
        $hash = null;
        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);
            if ($key === 'ts') {
                $ts = $value;
            } elseif ($key === 'v1') {
                $hash = $value;
            }
        }

        if ($ts === null || $hash === null) {
            return false;
        }

        $dataId = strtolower((string)($request->getQuery('data.id') ?? $request->getQuery('data_id') ?? ''));

        $parts = [];
        if ($dataId !== '') {
            $parts[] = "id:{$dataId}";
        }
        if ($requestIdHeader !== '') {
            $parts[] = "request-id:{$requestIdHeader}";
        }
        $parts[] = "ts:{$ts}";
        $manifest = implode(';', $parts) . ';';

        $computed = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($computed, $hash);
    }
}
