<?php

declare(strict_types=1);

namespace Hunter\Core\Contracts;

interface ModuleManifest
{
    public function identifier(): string;

    public function name(): string;

    public function description(): string;

    public function version(): string;

    /**
     * @return array{name: string, email?: string, url?: string}|null
     */
    public function author(): ?array;

    public function requiredPlatformVersion(): ?string;

    /**
     * @return array<string, string>
     */
    public function dependencies(): array;
}
