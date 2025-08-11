<?php

use App\Http\Controllers\GcashViewController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\SocialController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/auth/{provider}/redirect', [SocialController::class, 'redirect'])
    ->where('provider', 'google');
Route::get('/auth/{provider}/callback', [SocialController::class, 'callback'])
    ->where('provider', 'google');

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('gcash.dashboard')
        : redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Route::get('/dashboard', function () {
    //     return Inertia::render('Dashboard');
    // })->name('dashboard');
    Route::get('/gcash/transactions', [GcashViewController::class, 'index'])->name('gcash.transactions');
Route::get('/dashboard', [IncomeController::class, 'index'])->name('gcash.dashboard');
});

// Route::get('/admin', function(){
//     return Inertia::render('Admin/Dashboard');
// })->middleware(['auth', 'role:admin'])->name('admin');

// Route::group(['middleware' => ['role:admin']], function () { 
//     Route::get('/admin',function(){
//         return Inertia::render('Admin/Dashboard');
//     });
    
//  });

// require __DIR__ .'/api.php';