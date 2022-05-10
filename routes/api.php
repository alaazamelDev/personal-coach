<?php

use App\Http\Controllers\FoodController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('foods')->group(function () {
    Route::get('/', [FoodController::class, 'index']);
    Route::post('/', [FoodController::class, 'save']);
    Route::put('/{food}', [FoodController::class, 'update']);
    Route::delete('/', [FoodController::class, 'destroy']);

    // called only one time to fill the initial passed data
    Route::post('/init', [FoodController::class, 'initialize']);


    Route::get('/is_updated', [FoodController::class, 'isUpdated']);
});
