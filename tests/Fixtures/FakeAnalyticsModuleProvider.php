<?php

declare(strict_types=1);

namespace Hunter\Core\Tests\Fixtures;

use Hunter\Core\Contracts\ModuleServiceProvider;
use Hunter\Core\Module\Module;
use Hunter\Core\Navigation\NavGroup;
use Hunter\Core\Navigation\NavItem;

final class FakeAnalyticsModuleProvider extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module
            ->identifier('hunter/analytics')
            ->name('hunter-analytics')
            ->description('Analytics and reporting module')
            ->version('1.0.0')
            ->navigation([
                NavItem::make('Analytics')
                    ->href('/analytics')
                    ->order(10),
                NavGroup::make('Reports')
                    ->order(20),
            ]);
    }
}
