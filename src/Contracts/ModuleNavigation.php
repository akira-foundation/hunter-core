<?php

declare(strict_types=1);

namespace Hunter\Core\Contracts;

use Hunter\Core\Navigation\NavGroup;
use Hunter\Core\Navigation\NavItem;

interface ModuleNavigation
{
    /**
     * @return array<int, NavItem|NavGroup>
     */
    public function navigation(): array;
}
