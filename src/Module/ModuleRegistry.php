<?php

declare(strict_types=1);

namespace Hunter\Core\Module;

use Hunter\Core\Contracts\ModuleServiceProvider;
use Hunter\Core\Navigation\NavGroup;
use Hunter\Core\Navigation\NavItem;

use function count;

final class ModuleRegistry
{
    /** @var array<string, ModuleServiceProvider> */
    private array $modules = [];

    /**
     * Register a module with the registry.
     */
    public function register(ModuleServiceProvider $provider): void
    {
        $this->modules[$provider->identifier()] = $provider;
    }

    /**
     * Check if a module is registered.
     */
    public function has(string $identifier): bool
    {
        return isset($this->modules[$identifier]);
    }

    /**
     * Get a registered module by identifier.
     */
    public function get(string $identifier): ?ModuleServiceProvider
    {
        return $this->modules[$identifier] ?? null;
    }

    /**
     * Get all registered modules.
     *
     * @return array<string, ModuleServiceProvider>
     */
    public function all(): array
    {
        return $this->modules;
    }

    /**
     * Get all module identifiers.
     *
     * @return array<int, string>
     */
    public function identifiers(): array
    {
        return array_keys($this->modules);
    }

    /**
     * Get combined navigation from all registered modules.
     *
     * @return array<int, NavItem|NavGroup>
     */
    public function navigation(): array
    {
        $navigation = [];

        foreach ($this->modules as $provider) {
            $navigation = [...$navigation, ...$provider->navigation()];
        }

        return $this->sortNavigation($navigation);
    }

    /**
     * Get navigation items only (excludes groups).
     *
     * @return array<int, NavItem>
     */
    public function navigationItems(): array
    {
        return array_values(
            array_filter(
                $this->navigation(),
                static fn (NavItem|NavGroup $item): bool => $item instanceof NavItem,
            ),
        );
    }

    /**
     * Get navigation groups only (excludes standalone items).
     *
     * @return array<int, NavGroup>
     */
    public function navigationGroups(): array
    {
        return array_values(
            array_filter(
                $this->navigation(),
                static fn (NavItem|NavGroup $item): bool => $item instanceof NavGroup,
            ),
        );
    }

    /**
     * Get count of registered modules.
     */
    public function count(): int
    {
        return count($this->modules);
    }

    /**
     * Clear all registered modules.
     */
    public function clear(): void
    {
        $this->modules = [];
    }

    /**
     * Sort navigation items by their order property.
     *
     * @param  array<int, NavItem|NavGroup> $navigation
     * @return array<int, NavItem|NavGroup>
     */
    private function sortNavigation(array $navigation): array
    {
        usort(
            $navigation,
            static fn (NavItem|NavGroup $a, NavItem|NavGroup $b): int => $a->order <=> $b->order,
        );

        return $navigation;
    }
}
