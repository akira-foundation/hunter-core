<?php

declare(strict_types=1);

namespace Hunter\Module\Contracts;

use Hunter\Module\Navigation\NavGroup;
use Hunter\Module\Navigation\NavItem;

interface ModuleNavigation
{
    /**
     * @return array<int, NavItem|NavGroup>
     */
    public function navigation(): array;
}
