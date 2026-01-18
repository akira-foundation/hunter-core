<?php

declare(strict_types=1);

namespace Hunter\Module\Enums;

use function in_array;

enum ModuleLogStatus: string
{
    case Started   = 'started';
    case Completed = 'completed';
    case Failed    = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Started   => 'Started',
            self::Completed => 'Completed',
            self::Failed    => 'Failed',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Failed], true);
    }
}
