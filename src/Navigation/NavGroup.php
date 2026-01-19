<?php

declare(strict_types=1);

namespace Hunter\Module\Navigation;

final class NavGroup
{
    public string $title {
        get => $this->titleValue;
    }

    /** @var array<int, NavItem|NavGroup> */
    public array $items {
        get => $this->itemsValue;
    }

    public ?string $icon {
        get => $this->iconValue;
    }

    public int $order {
        get => $this->orderValue;
    }

    public bool $collapsible {
        get => $this->collapsibleValue;
    }

    public bool $collapsed {
        get => $this->collapsedValue;
    }

    private string $titleValue = '';

    /** @var array<int, NavItem|NavGroup> */
    private array $itemsValue = [];

    private ?string $iconValue = null;

    private int $orderValue = 0;

    private bool $collapsibleValue = true;

    private bool $collapsedValue = false;

    public static function make(?string $title = null): self
    {
        $instance = new self();

        if ($title !== null) {
            $instance->titleValue = $title;
        }

        return $instance;
    }

    public function title(string $title): self
    {
        $this->titleValue = $title;

        return $this;
    }

    public function icon(?string $icon): self
    {
        $this->iconValue = $icon;

        return $this;
    }

    public function order(int $order): self
    {
        $this->orderValue = $order;

        return $this;
    }

    public function collapsible(bool $collapsible = true): self
    {
        $this->collapsibleValue = $collapsible;

        return $this;
    }

    public function collapsed(bool $collapsed = true): self
    {
        $this->collapsedValue = $collapsed;

        return $this;
    }

    /**
     * @param  array<int, NavItem|NavGroup>  $items
     */
    public function items(array $items): self
    {
        $this->itemsValue = $items;

        return $this;
    }

    public function item(NavItem $item): self
    {
        $this->itemsValue[] = $item;

        return $this;
    }

    public function group(NavGroup $group): self
    {
        $this->itemsValue[] = $group;

        return $this;
    }

    /**
     * @return array<int, NavItem|NavGroup>
     */
    public function sortedItems(): array
    {
        $items = $this->items;

        usort($items, static fn (NavItem|NavGroup $a, NavItem|NavGroup $b): int => $a->order <=> $b->order);

        return $items;
    }

    /**
     * @return array{title: string, items: array<int, array<string, mixed>>, icon: string|null, order: int, collapsible: bool, collapsed: bool}
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'items' => array_map(
                static fn (NavItem|NavGroup $item): array => $item->toArray(),
                $this->sortedItems(),
            ),
            'icon'        => $this->icon,
            'order'       => $this->order,
            'collapsible' => $this->collapsible,
            'collapsed'   => $this->collapsed,
        ];
    }
}
