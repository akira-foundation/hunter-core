<?php

declare(strict_types=1);

namespace Hunter\Core\Commands;

use Illuminate\Console\Command;

final class CoreCommand extends Command
{
    public $signature = 'hunter-core';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
