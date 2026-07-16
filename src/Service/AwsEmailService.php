<?php

declare(strict_types=1);

namespace App\Service;

use Throwable;

/**
 * Envia e-mails através do servidor HTTP configurado em Settings.aws_api —
 * mesmo padrão já usado em outros pontos do projeto (contato, recuperação de
 * senha legada): POST form-encoded para "{aws_api}/send" com
 * destination/body/subject/from.
 */
class AwsEmailService
{
    private SettingsService $_settingsService;

    public function __construct(?SettingsService $settingsService = null)
    {
        $this->_settingsService = $settingsService ?? new SettingsService();
    }

    /**
     * Melhor-esforço: nunca lança exceção. Retorna false se não há servidor
     * configurado ou se o envio falhar — quem chama não deve travar o fluxo
     * principal (redefinição de senha, liberação de acesso) por causa disso.
     */
    public function send(string $toEmail, string $subject, string $htmlBody): bool
    {
        $settings = $this->_settingsService->get();

        if (empty($settings->aws_api)) {
            return false;
        }

        $data = [
            'destination' => trim($toEmail),
            'body' => $htmlBody,
            'subject' => $subject,
            'from' => "{$settings->title} <{$settings->email}>",
        ];

        $url = rtrim((string)$settings->aws_api, '/') . '/send';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $output = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $output !== false && $httpCode >= 200 && $httpCode < 300;
        } catch (Throwable $e) {
            return false;
        }
    }
}
