<?php

declare(strict_types=1);

namespace Hunter\Core\Tests\Fixtures;

use Hunter\Core\Contracts\ModuleServiceProvider;
use Hunter\Core\Module\Module;
use Hunter\Core\Navigation\NavItem;

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
