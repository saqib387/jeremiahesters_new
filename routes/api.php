<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StreamController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('web')->group(function () {
    // WebRTC signaling
    Route::post('/streams/{stream}/start', [StreamController::class, 'start']);
    Route::post('/streams/{stream}/end', [StreamController::class, 'end']);
    Route::post('/streams/{stream}/ice-candidate', [StreamController::class, 'iceCandidate']);
    Route::post('/streams/{stream}/answer', [StreamController::class, 'answer']);
    
    // Chat
    Route::get('/streams/{stream}/messages', [StreamController::class, 'getMessages']);
    Route::post('/streams/{stream}/messages', [StreamController::class, 'sendMessage']);
});
