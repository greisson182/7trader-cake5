<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;

class CurrencyHelper extends Helper
{
    /**
     * Formatar valor monetário baseado na moeda
     *
     * @param float $value Valor a ser formatado
     * @param string $currency Moeda (BRL ou USD)
     * @return string Valor formatado
     */
    public static function format(float $value, string $currency = 'BRL'): string
    {
        switch ($currency) {
            case 'USD':
                return '$' . number_format($value, 2, '.', ',');
            case 'BRL':
            default:
                return 'R$ ' . number_format($value, 2, ',', '.');
        }
    }

    /**
     * Obter símbolo da moeda
     *
     * @param string $currency Moeda (BRL ou USD)
     * @return string Símbolo da moeda
     */
    public static function getSymbol(string $currency = 'BRL'): string
    {
        switch ($currency) {
            case 'USD':
                return '$';
            case 'BRL':
            default:
                return 'R$';
        }
    }

    /**
     * Obter nome completo da moeda
     *
     * @param string $currency Moeda (BRL ou USD)
     * @return string Nome completo da moeda
     */
    public static function getName(string $currency = 'BRL'): string
    {
        switch ($currency) {
            case 'USD':
                return 'US Dollar';
            case 'BRL':
            default:
                return 'Brazilian Real';
        }
    }

    /**
     * Converter valor entre moedas (simulação - em produção usar API de câmbio)
     *
     * @param float $value Valor a ser convertido
     * @param string $fromCurrency Moeda de origem
     * @param string $toCurrency Moeda de destino
     * @return float Valor convertido
     */
    public static function convert(float $value, string $fromCurrency, string $toCurrency): float
    {
        // Taxa de câmbio simulada (em produção, usar API real)
        $exchangeRates = [
            'BRL_TO_USD' => 0.1886, // 1 BRL = 0.20 USD (aproximadamente)
            'USD_TO_BRL' => 5.30  // 1 USD = 5.00 BRL (aproximadamente)
        ];

        if ($fromCurrency === $toCurrency) {
            return $value;
        }

        if ($fromCurrency === 'BRL' && $toCurrency === 'USD') {
            return $value * $exchangeRates['BRL_TO_USD'];
        }

        if ($fromCurrency === 'USD' && $toCurrency === 'BRL') {
            return $value * $exchangeRates['USD_TO_BRL'];
        }

        return $value; // Retorna o valor original se não houver conversão disponível
    }

    /**
     * Formata valor para exibição na moeda preferida do usuário
     *
     * @param float|null $value Valor original (sempre em BRL no banco)
     * @param string $userCurrency Moeda preferida do usuário
     * @return string Valor formatado na moeda do usuário
     */
    public static function formatForUser(?float $value, string $userCurrency = 'BRL'): string
    {
        // Se o valor for null, retorna 0 formatado
        if ($value === null) {
            $value = 0.0;
        }

        if ($userCurrency === 'USD') {
            $convertedValue = self::convert($value, 'BRL', 'USD');
            return self::format($convertedValue, 'USD');
        }

        return self::format($value, 'BRL');
    }
}