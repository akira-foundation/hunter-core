<?php

declare(strict_types=1);

namespace Hunter\Core\Navigation;

final readonly class NavGroup
{
    /**
     * @param string              $title       Display title for the navigation group
     * @param array<int, NavItem> $items       Navigation items within this group
     * @param string|null         $icon        Icon identifier (e.g., Lucide icon name)
     * @param int                 $order       Sort order (lower values appear first)
     * @param bool                $collapsible Whether the group can be collapsed
     * @param bool                $collapsed   Default collapsed state
     */
    public function __construct(
        public string $title,
        public array $items = [],
        public ?string $icon = null,
        public int $order = 0,
        public bool $collapsible = true,
        public bool $collapsed = false,
    ) {}

    public function withItem(NavItem $item): self
    {
        return new self(
            title: $this->title,
            items: [...$this->items, $item],
            icon: $this->icon,
            order: $this->order,
            collapsible: $this->collapsible,
            collapsed: $this->collapsed,
        );
    }

    /**
     * @param array<int, NavItem> $items
     */
    public function withItems(array $items): self
    {
        return new self(
            title: $this->title,
            items: [...$this->items, ...$items],
            icon: $this->icon,
            order: $this->order,
            collapsible: $this->collapsible,
            collapsed: $this->collapsed,
        );
    }

    /**
     * @return array<int, NavItem>
     */
    public function sortedItems(): array
    {
        $items = $this->items;

        usort($items, static fn (NavItem $a, NavItem $b): int => $a->order <=> $b->order);

        return $items;
    }

    /**
     * @return array{title: string, items: array<int, array{title: string, href: string, icon: string|null, order: int, badge: string|null, external: bool}>, icon: string|null, order: int, collapsible: bool, collapsed: bool}
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'items' => array_map(
                static fn (NavItem $item): array => $item->toArray(),
                $this->sortedItems(),
            ),
            'icon'        => $this->icon,
            'order'       => $this->order,
            'collapsible' => $this->collapsible,
            'collapsed'   => $this->collapsed,
        ];
    }
}
