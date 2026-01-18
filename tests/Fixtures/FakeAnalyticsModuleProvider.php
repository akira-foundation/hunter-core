<?php

declare(strict_types=1);

namespace Hunter\Module\Tests\Fixtures;

use Hunter\Module\Contracts\ModuleServiceProvider;
use Hunter\Module\Module\Module;
use Hunter\Module\Navigation\NavGroup;
use Hunter\Module\Navigation\NavItem;

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
