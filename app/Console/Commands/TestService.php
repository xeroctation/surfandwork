<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\HomeService;
use App\Services\VerificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Test:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $max = DB::table('wallet_balances')->max('id') + 1;
        DB::statement("ALTER TABLE wallet_balances AUTO_INCREMENT =  $max");
    }
}
