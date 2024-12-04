<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::get('/getAssistants', [App\Http\Controllers\ChatBotController::class, 'getAssistants']);
// Route::get('/getAssistantsBD', [App\Http\Controllers\ChatBotController::class, 'getAssistantsBD']);
// Route::get('/getAssistantsByIDBD/{assistants_id}', [App\Http\Controllers\ChatBotController::class, 'getAssistantsByIDBD']);
// Route::get('/getAssistantsById/{id}', [App\Http\Controllers\ChatBotController::class, 'getAssistantById']);
// Route::get('/uploadFileStatic', [App\Http\Controllers\ChatBotController::class, 'uploadFileStatic']);
// Route::get('/listFilesStatic', [App\Http\Controllers\ChatBotController::class, 'listFilesStatic']);
// Route::get('/vectorCreateStatic', [App\Http\Controllers\ChatBotController::class, 'vectorCreateStatic']);
// Route::get('/listVectorStoresStatic', [App\Http\Controllers\ChatBotController::class, 'listVectorStoresStatic']);
// Route::get('/createVectorStoreFileStatic', [App\Http\Controllers\ChatBotController::class, 'createVectorStoreFileStatic']);

// Route::get('/new', [App\Http\Controllers\ChatBotController::class, 'newAssistantStatic']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
