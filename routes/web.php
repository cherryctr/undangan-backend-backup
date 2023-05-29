<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});

/**
 * route for admin
 */

//group route with prefix "admin"
Route::prefix('admin')->group(function () {

    //group route with middleware "auth"
    Route::group(['middleware' => 'auth'], function() {

        //route dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.index');


        Route::resource('/fitur', FiturController::class, ['as' => 'admin']);
        Route::resource('/desain', DesainController::class, ['as' => 'admin']);
        Route::resource('/category', CategoryController::class, ['as' => 'admin']);
        Route::resource('/product', ProductController::class, ['as' => 'admin']);


        Route::resource('/order', OrderController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'admin']);
        Route::resource('/slider', SliderController::class, ['except' => ['show', 'create', 'edit', 'update'], 'as' => 'admin']);

        Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
        Route::get('/customer', [CustomerController::class, 'index'])->name('admin.customer.index');

        //route user
        Route::resource('/user', UserController::class, ['except' => ['show'], 'as' => 'admin']);


    });
});
