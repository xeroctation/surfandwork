<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\HomeService;
use App\Services\VerificationService;
use Illuminate\Console\Command;

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
        $service = new VerificationService();
        $user = User::find(1); // replace with the ID of the user you want to verify
        $item = $service->send_verification($user);
    }
}
