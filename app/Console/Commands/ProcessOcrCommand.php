<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\BusinessLogicController;
use Illuminate\Support\Facades\Log;

class ProcessOcrCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-ocr-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will classify if the image contains a business card?';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Log::info("Processing OCR Images function: " . date('Y-m-d H:i:s'));
        $controller = new WhatsAppController();
        $response = $controller->classifyTheCard();
        Log::info("Processing Business Card Function: " . date('Y-m-d H:i:s'));
        $controller = new BusinessLogicController();
        $response = $controller->processBusinessCardUsingAI();
    }
}
