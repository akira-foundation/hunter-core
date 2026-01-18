<?php

declare(strict_types=1);

namespace Hunter\Module\Module;

use Hunter\Module\Navigation\NavGroup;
use Hunter\Module\Navigation\NavItem;
use Spatie\LaravelPackageTools\Package;

final class Module
{
    public string $identifier = '' {
        get {
            return $this->identifier;
        }
    }

    public string $name = '' {
        get {
            return $this->name;
        }
    }

    public string $description = '' {
        get {
            return $this->description;
        }
    }

    public string $version = '1.0.0' {
        get {
            return $this->version;
        }
    }

    /** @var array<int, NavItem|NavGroup> */
    public array $navigation = [] {
        get {
            return $this->navigation;
        }
    }

    /** @var array{name: string, email: string|null, url: string|null}|null */
    public ?array $author = null {
        get {
            return $this->author;
        }
    }

    public ?string $requiredPlatformVersion = null {
        get {
            return $this->requiredPlatformVersion;
        }
    }

    /** @var array<string, string> */
    public array $dependencies = [] {
        get {
            return $this->dependencies;
        }
    }

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

    public function identifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function version(string $version): self
    {
        $this->version = $version;

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

    /**
     * @param array<int, NavItem|NavGroup> $items
     */
    public function navigation(array $items): self
    {
        $this->navigation = $items;

        return $this;
    }

    public function author(string $name, ?string $email = null, ?string $url = null): self
    {
        $this->author = [
            'name'  => $name,
            'email' => $email,
            'url'   => $url,
        ];

        return $this;
    }

    public function requiredPlatformVersion(string $version): self
    {
        $this->requiredPlatformVersion = $version;

        return $this;
    }

    /**
     * @param array<string, string> $dependencies
     */
    public function dependencies(array $dependencies): self
    {
        $this->dependencies = $dependencies;

        return $this;
    }
}
