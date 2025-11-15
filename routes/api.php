<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\BusinessLogicController;

Route::post('/v1/getLeads', [BusinessLogicController::class, 'getLeads']);