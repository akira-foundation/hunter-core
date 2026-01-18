<?php

declare(strict_types=1);

namespace Hunter\Module\Enums;

use function in_array;

enum ModuleStatus: string
{
    case Pending    = 'pending';
    case Installing = 'installing';
    case Installed  = 'installed';
    case Failed     = 'failed';
    case Updating   = 'updating';
    case Removing   = 'removing';

    public function label(): string
    {
        return match ($this) {
            self::Pending    => 'Pending',
            self::Installing => 'Installing',
            self::Installed  => 'Installed',
            self::Failed     => 'Failed',
            self::Updating   => 'Updating',
            self::Removing   => 'Removing',
        };
    }

    public function isOperational(): bool
    {
        return $this === self::Installed;
    }

    public function isTransitional(): bool
    {
        return in_array($this, [
            self::Installing,
            self::Updating,
            self::Removing,
        ], true);
    }
}
