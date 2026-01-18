<?php

declare(strict_types=1);

namespace Hunter\Module\Tests\Fixtures;

use Hunter\Module\Contracts\ModuleServiceProvider;
use Hunter\Module\Module\Module;
use Hunter\Module\Navigation\NavGroup;
use Hunter\Module\Navigation\NavItem;

final class FakeCrmModuleProvider extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module
            ->identifier('hunter/crm')
            ->name('hunter-crm')
            ->description('Customer relationship management')
            ->version('2.0.0')
            ->navigation([
                NavItem::make('Contacts')
                    ->href('/contacts')
                    ->order(5),
                NavGroup::make('Sales')
                    ->order(15),
            ]);
    }
}
