<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MyCutomCommand extends Command
{
    protected $signature = 'say:hello';
    protected $description = 'Prints a greeting message';

    public function handle()
    {
        $this->info('გამარჯობა! ეს არის თქვენი პირველი artisan ბრძანება.');
        return 0;
    }
}
