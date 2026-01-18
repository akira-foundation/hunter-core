<?php

declare(strict_types=1);

namespace Hunter\Core\Tests\Fixtures;

use Hunter\Core\Contracts\ModuleServiceProvider;
use Hunter\Core\Module\Module;
use Hunter\Core\Navigation\NavGroup;
use Hunter\Core\Navigation\NavItem;

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
