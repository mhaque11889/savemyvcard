<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\BusinessLogicController;
use Illuminate\Support\Facades\Log;

class ProcessBusinessCardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-business-card-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        Log::info("Processing Business Card: " . date('Y-m-d H:i:s'));
        $controller = new BusinessLogicController();
        $response = $controller->processBusinessCardUsingAI();
    }
}
