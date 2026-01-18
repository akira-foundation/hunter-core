<?php

declare(strict_types=1);

namespace Hunter\Core\Module;

use Spatie\LaravelPackageTools\Package;

final class Module
{
    private string $name = '';

    private bool $hasConfig = false;

    private bool $hasViews = false;

    private bool $hasRoutes = false;

    private bool $hasTranslations = false;

    /** @var array<int, string> */
    private array $migrations = [];

    /** @var array<int, class-string> */
    private array $commands = [];

    public function __construct(
        private readonly Package $package,
    ) {}

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function hasConfig(): self
    {
        $this->hasConfig = true;

        return $this;
    }

    public function hasViews(): self
    {
        $this->hasViews = true;

        return $this;
    }

    public function hasRoutes(): self
    {
        $this->hasRoutes = true;

        return $this;
    }

    public function hasTranslations(): self
    {
        $this->hasTranslations = true;

        return $this;
    }

    public function hasMigration(string $migration): self
    {
        $this->migrations[] = $migration;

        return $this;
    }

    /**
     * @param array<int, string> $migrations
     */
    public function hasMigrations(array $migrations): self
    {
        $this->migrations = [...$this->migrations, ...$migrations];

        return $this;
    }

    /**
     * @param class-string $command
     */
    public function hasCommand(string $command): self
    {
        $this->commands[] = $command;

        return $this;
    }

    /**
     * @param array<int, class-string> $commands
     */
    public function hasCommands(array $commands): self
    {
        $this->commands = [...$this->commands, ...$commands];

        return $this;
    }

    /**
     * Apply all configurations to the underlying Spatie Package.
     */
    public function apply(): void
    {
        $this->package->name($this->name);

        if ($this->hasConfig) {
            $this->package->hasConfigFile();
        }

        if ($this->hasViews) {
            $this->package->hasViews();
        }

        if ($this->hasRoutes) {
            $this->package->hasRoutes($this->name);
        }

        if ($this->hasTranslations) {
            $this->package->hasTranslations();
        }

        foreach ($this->migrations as $migration) {
            $this->package->hasMigration($migration);
        }

        foreach ($this->commands as $command) {
            $this->package->hasCommand($command);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}
