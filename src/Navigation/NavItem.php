<?php

declare(strict_types=1);

namespace Hunter\Core\Navigation;

final class NavItem
{
    public string $title {
        get => $this->titleValue;
    }

    public string $href {
        get => $this->hrefValue;
    }

    public ?string $icon {
        get => $this->iconValue;
    }

    public int $order {
        get => $this->orderValue;
    }

    public ?string $badge {
        get => $this->badgeValue;
    }

    public bool $external {
        get => $this->externalValue;
    }

    private string $titleValue = '';

    private string $hrefValue = '';

    private ?string $iconValue = null;

    private int $orderValue = 0;

    private ?string $badgeValue = null;

    private bool $externalValue = false;

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

    public function href(string $href): self
    {
        $this->hrefValue = $href;

        return $this;
    }

    public function url(string $url): self
    {
        return $this->href($url);
    }

    public function route(string $route): self
    {
        return $this->href($route);
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

    public function badge(?string $badge): self
    {
        $this->badgeValue = $badge;

        return $this;
    }

    public function external(bool $external = true): self
    {
        $this->externalValue = $external;

        return $this;
    }

    public function openInNewTab(): self
    {
        return $this->external(true);
    }

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
