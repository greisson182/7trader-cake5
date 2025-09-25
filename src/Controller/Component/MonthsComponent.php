<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;

class MonthsComponent extends Component
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
     * Converte número do mês para nome em português
     * 
     * @param int $monthNumber Número do mês (1-12)
     * @param bool $short Se true, retorna versão abreviada (Jan, Fev, etc.)
     * @return string Nome do mês em português ou string vazia se inválido
     */
    public static function getMonthNamePortuguese(int $monthNumber, bool $short = false): string
    {
        if ($monthNumber < 1 || $monthNumber > 12) {
            return '';
        }

        $monthsArray = $short ? self::$monthsPortugueseShort : self::$monthsPortuguese;
        
        return $monthsArray[$monthNumber];
    }

    /**
     * Converte número do mês e ano para formato "Mês Ano" em português
     * 
     * @param int $monthNumber Número do mês (1-12)
     * @param int $year Ano
     * @param bool $short Se true, usa versão abreviada do mês
     * @return string Formato "Janeiro 2025" ou "Jan 2025"
     */
    public static function getMonthYearPortuguese(int $monthNumber, int $year, bool $short = false): string
    {
        $monthName = self::getMonthNamePortuguese($monthNumber, $short);
        
        if (empty($monthName)) {
            return '';
        }

        return $monthName . ' ' . $year;
    }

    /**
     * Obtém todos os nomes dos meses em português
     * 
     * @param bool $short Se true, retorna versões abreviadas
     * @return array Array associativo com números dos meses como chaves e nomes como valores
     */
    public static function getAllMonthsPortuguese(bool $short = false): array
    {
        return $short ? self::$monthsPortugueseShort : self::$monthsPortuguese;
    }

    /**
     * Converte data no formato Y-m-d para formato brasileiro com mês em português
     * 
     * @param string $date Data no formato Y-m-d (ex: 2025-01-15)
     * @param bool $shortMonth Se true, usa versão abreviada do mês
     * @return string Data formatada (ex: "15 de Janeiro de 2025" ou "15 Jan 2025")
     */
    public static function formatDatePortuguese(string $date, bool $shortMonth = false): string
    {
        $timestamp = strtotime($date);
        
        if ($timestamp === false) {
            return $date; // Retorna a data original se não conseguir converter
        }

        $day = date('j', $timestamp);
        $month = (int)date('n', $timestamp);
        $year = date('Y', $timestamp);

        $monthName = self::getMonthNamePortuguese($month, $shortMonth);

        if ($shortMonth) {
            return $day . ' ' . $monthName . ' ' . $year;
        } else {
            return $day . ' de ' . $monthName . ' de ' . $year;
        }
    }

    /**
     * Valida se um número é um mês válido
     * 
     * @param int $monthNumber Número a ser validado
     * @return bool True se for um mês válido (1-12)
     */
    public static function isValidMonth(int $monthNumber): bool
    {
        return $monthNumber >= 1 && $monthNumber <= 12;
    }

    /**
     * Obtém o número do mês atual
     * 
     * @return int Número do mês atual (1-12)
     */
    public static function getCurrentMonth(): int
    {
        return (int)date('n');
    }

    /**
     * Obtém o nome do mês atual em português
     * 
     * @param bool $short Se true, retorna versão abreviada
     * @return string Nome do mês atual em português
     */
    public static function getCurrentMonthName(bool $short = false): string
    {
        return self::getMonthNamePortuguese(self::getCurrentMonth(), $short);
    }

    /**
     * Trata datas no formato brasileiro (dd/mm/aaaa)
     * Converte para formato americano (Y-m-d) ou outros formatos
     * 
     * @param string $dateBr Data no formato brasileiro (dd/mm/aaaa)
     * @param string $outputFormat Formato de saída desejado (padrão: 'Y-m-d')
     * @return string|false Data formatada ou false se inválida
     */
    public static function treatBrazilianDate(string $dateBr, string $outputFormat = 'Y-m-d')
    {
        // Remove espaços em branco
        $dateBr = trim($dateBr);
        
        // Verifica se está no formato dd/mm/aaaa
        if (!preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateBr, $matches)) {
            return false;
        }
        
        $day = (int)$matches[1];
        $month = (int)$matches[2];
        $year = (int)$matches[3];
        
        // Valida se a data é válida
        if (!checkdate($month, $day, $year)) {
            return false;
        }
        
        // Cria timestamp
        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        
        // Retorna no formato solicitado
        return date($outputFormat, $timestamp);
    }

    /**
     * Converte data brasileira para formato americano (Y-m-d)
     * 
     * @param string $dateBr Data no formato brasileiro (dd/mm/aaaa)
     * @return string|false Data no formato Y-m-d ou false se inválida
     */
    public static function brazilianToAmerican(string $dateBr)
    {
        return self::treatBrazilianDate($dateBr, 'Y-m-d');
    }

    /**
     * Converte data americana para formato brasileiro (dd/mm/aaaa)
     * 
     * @param string $dateAm Data no formato americano (Y-m-d)
     * @return string|false Data no formato dd/mm/aaaa ou false se inválida
     */
    public static function americanToBrazilian(string $dateAm)
    {
        $timestamp = strtotime($dateAm);
        
        if ($timestamp === false) {
            return false;
        }
        
        return date('d/m/Y', $timestamp);
    }

    /**
     * Valida se uma data brasileira está no formato correto e é válida
     * 
     * @param string $dateBr Data no formato brasileiro (dd/mm/aaaa)
     * @return bool True se a data for válida
     */
    public static function isValidBrazilianDate(string $dateBr): bool
    {
        return self::treatBrazilianDate($dateBr) !== false;
    }

    /**
     * Formata data brasileira com nome do mês em português
     * 
     * @param string $dateBr Data no formato brasileiro (dd/mm/aaaa)
     * @param bool $shortMonth Se true, usa versão abreviada do mês
     * @return string|false Data formatada em português ou false se inválida
     */
    public static function formatBrazilianDatePortuguese(string $dateBr, bool $shortMonth = false)
    {
        // Remove espaços em branco
        $dateBr = trim($dateBr);
        
        // Verifica se está no formato dd/mm/aaaa
        if (!preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateBr, $matches)) {
            return false;
        }
        
        $day = (int)$matches[1];
        $month = (int)$matches[2];
        $year = (int)$matches[3];
        
        // Valida se a data é válida
        if (!checkdate($month, $day, $year)) {
            return false;
        }
        
        $monthName = self::getMonthNamePortuguese($month, $shortMonth);
        
        if ($shortMonth) {
            return $day . ' ' . $monthName . ' ' . $year;
        } else {
            return $day . ' de ' . $monthName . ' de ' . $year;
        }
    }

    /**
     * Converte data do formato americano (YYYY-MM-DD) para formato brasileiro (DD/MM/YYYY)
     * 
     * @param string $date Data no formato americano (YYYY-MM-DD)
     * @return string Data no formato brasileiro (DD/MM/YYYY)
     */
    public static function dataView(string $date): string
    {
        if (empty($date)) {
            return '';
        }
        
        // Verifica se a data está no formato americano (YYYY-MM-DD)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $convertedDate = implode('/', array_reverse(explode('-', $date)));
            return $convertedDate;
        }
        
        // Se já estiver no formato brasileiro ou outro formato, retorna como está
        return $date;
    }

    /**
     * Obtém a data atual no formato brasileiro
     * 
     * @return string Data atual no formato dd/mm/aaaa
     */
    public static function getCurrentBrazilianDate(): string
    {
        return date('d/m/Y');
    }

    /**
     * Calcula a diferença em dias entre duas datas brasileiras
     * 
     * @param string $dateBr1 Primeira data (dd/mm/aaaa)
     * @param string $dateBr2 Segunda data (dd/mm/aaaa)
     * @return int|false Diferença em dias ou false se alguma data for inválida
     */
    public static function daysDifferenceBrazilian(string $dateBr1, string $dateBr2)
    {
        $date1 = self::treatBrazilianDate($dateBr1);
        $date2 = self::treatBrazilianDate($dateBr2);
        
        if ($date1 === false || $date2 === false) {
            return false;
        }
        
        $timestamp1 = strtotime($date1);
        $timestamp2 = strtotime($date2);
        
        $difference = $timestamp2 - $timestamp1;
        
        return (int)($difference / (60 * 60 * 24));
    }
}