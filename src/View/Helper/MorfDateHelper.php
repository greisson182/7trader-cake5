<?php
namespace App\View\Helper;

use Cake\View\Helper;

class MorfDateHelper extends Helper
{
    /**
     * Array com os nomes dos meses em português
     */
    private static $monthsPortuguese = [
        1 => 'Janeiro',
        2 => 'Fevereiro', 
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro'
    ];

    /**
     * Array com os nomes dos meses abreviados em português
     */
    private static $monthsPortugueseShort = [
        1 => 'Jan',
        2 => 'Fev', 
        3 => 'Mar',
        4 => 'Abr',
        5 => 'Mai',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Set',
        10 => 'Out',
        11 => 'Nov',
        12 => 'Dez'
    ];

    /**
     * Formatar data com mês em português
     * 
     * @param int $month Número do mês (1-12)
     * @param int $year Ano
     * @param bool $short Se true, usa abreviação do mês
     * @return string Data formatada em português
     */
    public static function formatMonthYear($month, $year, $short = true)
    {
        $monthsArray = $short ? self::$monthsPortugueseShort : self::$monthsPortuguese;
        
        if (isset($monthsArray[$month])) {
            return $monthsArray[$month] . ' ' . $year;
        }
        
        return date('M Y', mktime(0, 0, 0, $month, 1, $year));
    }

    /**
     * Obter nome do mês em português
     * 
     * @param int $month Número do mês (1-12)
     * @param bool $short Se true, retorna abreviação
     * @return string Nome do mês em português
     */
    public static function getMonthName($month, $short = false)
    {
        $monthsArray = $short ? self::$monthsPortugueseShort : self::$monthsPortuguese;
        
        return $monthsArray[$month] ?? '';
    }

    /**
     * Converter timestamp para formato português
     * 
     * @param string|int $timestamp Timestamp ou string de data
     * @param string $format Formato desejado (suporta 'M Y', 'F Y', etc.)
     * @return string Data formatada em português
     */
    public static function formatDatePortuguese($timestamp, $format = 'M Y')
    {
        $timestamp = is_string($timestamp) ? strtotime($timestamp) : $timestamp;
        
        if ($format === 'M Y') {
            $month = (int)date('n', $timestamp);
            $year = date('Y', $timestamp);
            return self::formatMonthYear($month, $year, true);
        }
        
        if ($format === 'F Y') {
            $month = (int)date('n', $timestamp);
            $year = date('Y', $timestamp);
            return self::formatMonthYear($month, $year, false);
        }
        
        // Para outros formatos, usar date() padrão
        return date($format, $timestamp);
    }
}