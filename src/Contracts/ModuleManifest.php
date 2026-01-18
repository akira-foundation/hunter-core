<?php

declare(strict_types=1);

namespace Hunter\Core\Contracts;

interface ModuleManifest
{
    /**
     * Get the unique identifier for this module.
     * Should match the Composer package name (e.g., 'hunter/analytics').
     */
    public function identifier(): string;

    /**
     * Get the human-readable name of the module.
     */
    public function name(): string;

    /**
     * Get the module description.
     */
    public function description(): string;

    /**
     * Get the module version.
     */
    public function version(): string;

    /**
     * Get the module author information.
     *
     * @return array{name: string, email?: string, url?: string}|null
     */
    public function author(): ?array;

    /**
     * Get the minimum required Hunter platform version.
     */
    public function requiredPlatformVersion(): ?string;

    /**
     * Get the module dependencies (other module identifiers).
     *
     * @return array<int, string>
     */
    public function dependencies(): array;
}
