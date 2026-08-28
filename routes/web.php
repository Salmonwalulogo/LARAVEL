<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;

Route::get('/', function () {
    return view('welcome');
});

// Routes untuk Product
Route::resource('products', ProductController::class);

// Routes untuk Customer
Route::resource('customers', CustomerController::class);

// Routes untuk Sale
Route::resource('sales', SaleController::class);