<?php

declare(strict_types=1);

namespace Hunter\Module\Tests\Fixtures;

use Hunter\Module\Contracts\ModuleServiceProvider;
use Hunter\Module\Module\Module;
use Hunter\Module\Navigation\NavItem;

final class TestModuleServiceProvider extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module
            ->identifier('test/module')
            ->name('test-module')
            ->description('Test module for testing')
            ->version('1.2.3')
            ->navigation([
                NavItem::make('Test')
                    ->href('/test')
                    ->order(1),
            ])
            ->author('Test Author', 'test@example.com', 'https://example.com')
            ->requiredPlatformVersion('2.0.0')
            ->dependencies(['hunter/core' => '^1.0']);
    }
}
