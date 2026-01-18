<?php

declare(strict_types=1);

namespace Hunter\Core\Tests\Fixtures;

use Hunter\Core\Contracts\ModuleServiceProvider;
use Hunter\Core\Module\Module;

final class MinimalModuleServiceProvider extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module->name('minimal');
    }
}
