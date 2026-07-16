<?php

declare(strict_types=1);

namespace App\Service\Exception;

use Exception;

/**
 * Lançada quando o e-mail informado no checkout já pertence a uma conta
 * ativa — a compra não é anexada automaticamente, o comprador precisa
 * fazer login antes de continuar.
 */
class AccountRequiresLoginException extends Exception
{
}
