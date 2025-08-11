
<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\ApiIncomeController;
use App\Http\Controllers\ApiLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GcashLoginController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MainDashboardController;
use App\Http\Controllers\SocialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
// Route::post('/login', [GcashLoginController::class, 'loginGcash']);
// Route::middleware('auth:sanctum')->get('/user', [GcashLoginController::class, 'user']);
// Route::post('/logout', [GcashLoginController::class, 'logout']);
Route::post('/add/transactions', [ApiController::class, 'store']);
Route::get('/transactions', [ApiController::class, 'index']);
Route::put('/update/transactions/{transaction}', [ApiController::class, 'update']);
Route::delete('/delete/transactions/{id}', [ApiController::class, 'destroy']);
Route::delete('/delete/all/transactions', [ApiController::class, 'deleteAll']);
Route::get('/income/income', [ApiController::class, 'stats']);
Route::get('/daily-transaction-count', [ApiController::class, 'getDailyTransactionCount']);
Route::post('/logout', [AuthController::class, 'logout']);
