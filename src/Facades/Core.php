<?php

declare(strict_types=1);

namespace Core\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Core\Core\Core
 */
final class Core extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Core\Core\Core::class;
    }
}
