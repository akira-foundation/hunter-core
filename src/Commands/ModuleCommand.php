<?php

declare(strict_types=1);

namespace Hunter\Module\Commands;

use Illuminate\Console\Command;

final class ModuleCommand extends Command
{
    public $signature = 'hunter:module';

    public $description = 'Hunter module system command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
