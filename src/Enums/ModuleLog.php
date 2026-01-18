<?php

declare(strict_types=1);

namespace Hunter\Core\Enums;

enum ModuleLog: string
{
    case Install    = 'install';
    case Update     = 'update';
    case Remove     = 'remove';
    case Publish    = 'publish';
    case Migrate    = 'migrate';
    case Build      = 'build';
    case Activate   = 'activate';
    case Deactivate = 'deactivate';

    public function label(): string
    {
        return match ($this) {
            self::Install    => 'Install',
            self::Update     => 'Update',
            self::Remove     => 'Remove',
            self::Publish    => 'Publish',
            self::Migrate    => 'Migrate',
            self::Build      => 'Build',
            self::Activate   => 'Activate',
            self::Deactivate => 'Deactivate',
        };
    }

    public function verb(): string
    {
        return match ($this) {
            self::Install    => 'installed',
            self::Update     => 'updated',
            self::Remove     => 'removed',
            self::Publish    => 'published',
            self::Migrate    => 'migrated',
            self::Build      => 'built',
            self::Activate   => 'activated',
            self::Deactivate => 'deactivated',
        };
    }
}
