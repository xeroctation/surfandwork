<?php

namespace App\Console\Commands;

use App\Services\HomeService;
use App\Services\NotificationService;
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
        $service = new HomeService();
        $item = $service->category(18);
        dd($item);
    }
}
