<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Public Web Routes
|--------------------------------------------------------------------------
*/

// Root and Home
Route::get('/', [UserController::class, 'index'])->name('home');
Route::get('/home', [UserController::class, 'index']);

// Voting Page
Route::get('/vote', [UserController::class, 'votepage'])->name('vote');


/*
|--------------------------------------------------------------------------
| Admin Routes (Grouped & Prefixed)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    /**
     * 1. Public Admin Entry Page
     * Accessible to everyone
     */
    Route::get('/', function () {
        return view('admin.welcome');
    })->name('admin.welcome');


    /**
     * 2. Authentication Routes (Guest Middleware Removed)
     * These are now accessible even if you are logged in
     */
    
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('admin.register');
    Route::post('/register', [AuthController::class, 'register'])->name('admin.register.submit');


    /**
     * 3. Protected Admin Routes
     * Only accessible after a successful login
     */
    Route::middleware('auth')->group(function () {
        
        // The colorful grid dashboard (admin.index)
        Route::get('/index', function () {
            return view('admin.index');
        })->name('admin.index');

        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        
    });
});