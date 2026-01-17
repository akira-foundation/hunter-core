<?php

declare(strict_types=1);

use Core\Core\Commands\CoreCommand;

use function Pest\Laravel\artisan;

it('can run the hunter-core command', function (): void {
    artisan(CoreCommand::class)
        ->expectsOutput('All done')
        ->assertSuccessful();
});

it('has correct signature', function (): void {
    $command = new CoreCommand();

    expect($command->signature)->toBe('hunter-core')
        ->and($command->description)->toBe('My command');
});
