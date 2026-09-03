<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckOverdueInvoices extends Command
{
    protected $signature = 'invoices:check-overdue';

    protected $description = 'Check for overdue invoices';

    public function handle(): int
    {
        $this->info('Overdue invoice checks are ready.');

        return self::SUCCESS;
    }
}