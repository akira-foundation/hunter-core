<?php

declare(strict_types=1);

namespace Hunter\Core\Navigation;

final readonly class NavItem
{
    /**
     * @param string      $title    Display title for the navigation item
     * @param string      $href     URL or route name for the navigation link
     * @param string|null $icon     Icon identifier (e.g., Lucide icon name)
     * @param int         $order    Sort order (lower values appear first)
     * @param string|null $badge    Optional badge text to display
     * @param bool        $external Whether the link opens in a new tab
     */
    public function __construct(
        public string $title,
        public string $href,
        public ?string $icon = null,
        public int $order = 0,
        public ?string $badge = null,
        public bool $external = false,
    ) {}

    /**
     * @return array{title: string, href: string, icon: string|null, order: int, badge: string|null, external: bool}
     */
    public function toArray(): array
    {
        return [
            'title'    => $this->title,
            'href'     => $this->href,
            'icon'     => $this->icon,
            'order'    => $this->order,
            'badge'    => $this->badge,
            'external' => $this->external,
        ];
    }
}
