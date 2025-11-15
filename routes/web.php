<?php

use App\Http\Controllers\RoutingController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\BusinessLogicController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MediaController;

use Illuminate\Support\Facades\Route;

Route::get('/', [RoutingController::class , 'index']);
Route::get('/signin', [RoutingController::class, 'memberSignin']);
Route::get('/register', [RoutingController::class, 'memberRegister']);
Route::get('/forgotPassword', [RoutingController::class, 'memberForgotPassword']);
Route::get('/changePassword', [RoutingController::class, 'changePassword']);
Route::post('/changePassword', [BusinessLogicController::class, 'changePassword']);

Route::get('captcha', [CaptchaController::class, 'generate']);
Route::get('/memberDashboard', [RoutingController::class, 'memberDashboard']);
Route::get('/vcards', [RoutingController::class, 'viewVCards']);
Route::get('/changeInteractionType/{id}',[RoutingController::class,'changeInteractionType']);
Route::get('/addNewVCards', [RoutingController::class, 'addNewVCards']);
Route::get('/leadNotification', [RoutingController::class, 'leadNotification']);
Route::get('/digitalCards', [RoutingController::class, 'digitalCards']);
Route::get('/digitalCards/{uniqueCode}', [RoutingController::class, 'digitalCardsNew']);
Route::get('/apiIntegrations', [RoutingController::class, 'apiIntegrations']);
Route::get('/sendTestMessage', [RoutingController::class, 'sendTestMessage']);
Route::get('/editDigitalCard/{id}', [RoutingController::class, 'editDigitalCard']);
Route::post('/loginMember', [LoginController::class, 'loginMember']);
Route::get('/logout', [LoginController::class, 'logout']);
Route::post('/registerCheck', [LoginController::class , 'registerMember']);
Route::post('/updateVCardInteractionType', [BusinessLogicController::class, 'updateVCardInteractionType']);
Route::get('/viewProcessedBusinessCard', [RoutingController::class, 'viewProcessedBusinessCard']);
Route::post('/getAllAlternateCodes', [BusinessLogicController::class, 'getAllAlternateCodes']);
Route::post('/webhook', [WhatsAppController::class, 'postWebhook']);
Route::get('/webhook', [WhatsAppController::class, 'getWebhook']);

Route::post('/getStates', [BusinessLogicController::class, 'getStates']);
Route::post('/getCities', [BusinessLogicController::class, 'getCities']);
Route::post('/saveVCards', [BusinessLogicController::class, 'saveVCard']);
Route::post('/updateVCard', [BusinessLogicController::class, 'updateVCard']);
Route::post( '/sendOTPEmail', [BusinessLogicController::class,'sendOTPEmail']);
Route::post( '/emailVerified', [BusinessLogicController::class,'emailVerified']);

Route::get( '/contactCard', [RoutingController::class,'contactCard']);
Route::get( '/qrcode', [RoutingController::class,'qrCode']);

Route::get( '/vcf/{cardcode}', [RoutingController::class,'createVcfCards'])->name('vcfcard');
Route::get('/processCard/{cardcode}', [BusinessLogicController::class, 'processCard']);

Route::get('/admin', [AdminController::class , 'index']);
Route::get('/adminlogin', [AdminController::class , 'index']);
Route::post('/loginAdmin', [AdminController::class , 'loginAdmin']);

Route::get('/adminDashboard', [AdminController::class , 'adminDashboard']);
Route::get('/registeredUsers', [AdminController::class , 'registeredUsers']);
Route::get('/nonRegisteredUsers', [AdminController::class , 'nonRegisteredUsers']);
Route::get('/viewProcessedCards', [AdminController::class , 'viewProcessedCards']);
Route::get('/viewAllVcards', [AdminController::class, 'viewAllVcards']);
Route::get('/userProfile/{id}', [AdminController::class , 'userProfile']);
Route::get('/loginImpersonate/{id}', [AdminController::class , 'loginImpersonate']);

Route::get('/verifyEmailByAdmin/{userid}', [AdminController::class , 'verifyEmailByAdmin']);
Route::get('/verifyMobileByAdmin/{userid}', [AdminController::class , 'verifyMobileByAdmin']);
Route::get('/verifyVcardByAdmin/{vcardid}', [AdminController::class , 'verifyVcardByAdmin']);
Route::get('/viewBusinessCardData', [AdminController::class, 'viewBusinessCardData']);
Route::post('/filterBusinessCardData', [AdminController::class, 'filterBusinessCardData'])->name('filterBusinessCardData');

Route::get('/failedMessages', [AdminController::class , 'failedMessages']);
Route::get('/media/{filename}', [MediaController::class, 'serveMedia']);

Route::get('/processBusinessCard', [BusinessLogicController::class, 'processBusinessCard']);
Route::get('/processBusinessCardAI', [BusinessLogicController::class, 'processBusinessCardUsingAI']);

Route::post('/addAlternateQR', [AdminController::class, 'addAlternateQRCode']);

Route::post('/editScannedBusinessCards', [BusinessLogicController::class, 'editScannedBusinessCards'])->name('editScannedBusinessCards');
Route::get('/update-scanned-card', [BusinessLogicController::class, 'updateScannedCard'])->name('updateScannedBusinessCard');

Route::get('/getDigitalCard/{id}', [RoutingController::class, 'digitalCardsDownloadAsImage']);

Route::get('/processBusinessCardUsingForManzar/{id}', [BusinessLogicController::class, 'processBusinessCardUsingForManzar']);

Route::get('/viewProcessedBusinessCardData', [RoutingController::class, 'viewProcessedBusinessCardData']);