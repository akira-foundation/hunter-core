<?php

declare(strict_types=1);

namespace Hunter\Module\Tests\Fixtures;

use Hunter\Module\Contracts\ModuleServiceProvider;
use Hunter\Module\Module\Module;

final class MinimalModuleServiceProvider extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module->name('minimal');
    }
}
