<?php

declare(strict_types=1);

use Hunter\Core\Enums\ModuleLogAction;
use Hunter\Core\Enums\ModuleLogStatus;
use Hunter\Core\Enums\ModuleStatus;

describe('ModuleStatus', function (): void {
    it('has all expected cases', function (): void {
        expect(ModuleStatus::cases())->toHaveCount(6)
            ->and(ModuleStatus::Pending->value)->toBe('pending')
            ->and(ModuleStatus::Installing->value)->toBe('installing')
            ->and(ModuleStatus::Installed->value)->toBe('installed')
            ->and(ModuleStatus::Failed->value)->toBe('failed')
            ->and(ModuleStatus::Updating->value)->toBe('updating')
            ->and(ModuleStatus::Removing->value)->toBe('removing');
    });

    it('returns correct labels', function (): void {
        expect(ModuleStatus::Pending->label())->toBe('Pending')
            ->and(ModuleStatus::Installed->label())->toBe('Installed');
    });

    it('identifies operational status', function (): void {
        expect(ModuleStatus::Installed->isOperational())->toBeTrue()
            ->and(ModuleStatus::Pending->isOperational())->toBeFalse()
            ->and(ModuleStatus::Installing->isOperational())->toBeFalse();
    });

    it('identifies transitional statuses', function (): void {
        expect(ModuleStatus::Installing->isTransitional())->toBeTrue()
            ->and(ModuleStatus::Updating->isTransitional())->toBeTrue()
            ->and(ModuleStatus::Removing->isTransitional())->toBeTrue()
            ->and(ModuleStatus::Installed->isTransitional())->toBeFalse()
            ->and(ModuleStatus::Pending->isTransitional())->toBeFalse();
    });
});

describe('ModuleLogAction', function (): void {
    it('has all expected cases', function (): void {
        expect(ModuleLogAction::cases())->toHaveCount(8)
            ->and(ModuleLogAction::Install->value)->toBe('install')
            ->and(ModuleLogAction::Update->value)->toBe('update')
            ->and(ModuleLogAction::Remove->value)->toBe('remove')
            ->and(ModuleLogAction::Activate->value)->toBe('activate')
            ->and(ModuleLogAction::Deactivate->value)->toBe('deactivate');
    });

    it('returns correct labels', function (): void {
        expect(ModuleLogAction::Install->label())->toBe('Install')
            ->and(ModuleLogAction::Activate->label())->toBe('Activate');
    });

    it('returns correct verbs', function (): void {
        expect(ModuleLogAction::Install->verb())->toBe('installed')
            ->and(ModuleLogAction::Activate->verb())->toBe('activated');
    });
});

describe('ModuleLogStatus', function (): void {
    it('has all expected cases', function (): void {
        expect(ModuleLogStatus::cases())->toHaveCount(3)
            ->and(ModuleLogStatus::Started->value)->toBe('started')
            ->and(ModuleLogStatus::Completed->value)->toBe('completed')
            ->and(ModuleLogStatus::Failed->value)->toBe('failed');
    });

    it('returns correct labels', function (): void {
        expect(ModuleLogStatus::Started->label())->toBe('Started')
            ->and(ModuleLogStatus::Completed->label())->toBe('Completed');
    });

    it('identifies terminal statuses', function (): void {
        expect(ModuleLogStatus::Completed->isTerminal())->toBeTrue()
            ->and(ModuleLogStatus::Failed->isTerminal())->toBeTrue()
            ->and(ModuleLogStatus::Started->isTerminal())->toBeFalse();
    });
});
