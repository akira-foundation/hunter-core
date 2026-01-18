<?php

declare(strict_types=1);

use Hunter\Module\Commands\ModuleCommand;

use function Pest\Laravel\artisan;

it('can run the hunter:module command', function (): void {
    artisan(ModuleCommand::class)
        ->expectsOutput('All done')
        ->assertSuccessful();
});

it('has correct signature', function (): void {
    $command = new ModuleCommand();

    expect($command->signature)->toBe('hunter:module')
        ->and($command->description)->toBe('Hunter module system command');
});
