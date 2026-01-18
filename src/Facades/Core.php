<?php

declare(strict_types=1);

namespace Hunter\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hunter\Core\Core
 */
final class Core extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Hunter\Core\Core::class;
    }
}
