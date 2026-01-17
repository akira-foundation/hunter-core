<?php

namespace Core\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Core\Core\Core
 */
class Core extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Core\Core\Core::class;
    }
}
