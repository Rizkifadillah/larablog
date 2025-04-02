<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

// TESTING ROUTE

Route::view('/page','page');
Route::view('/auth','auth');


// ADMIN ROUTES

Route::prefix('admin')->name('admin.')->group(function(){
    Route::middleware([])->group(function(){
        Route::controller(AuthController::class)->group(function(){
            Route::get('/login','loginForm')->name('login');
            Route::post('/login', 'loginHandler')->name('login_handler');
            Route::get('/forget-password','forgetForm')->name('forget');
        });
    });

    Route::middleware([])->group(function(){
        Route::controller(AdminController::class)->group(function(){
            Route::get('/dashboard','adminDashboard')->name('dashboard');
        });
    });
});