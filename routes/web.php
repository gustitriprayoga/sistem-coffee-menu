<?php


use Illuminate\Support\Facades\Route;
use App\Livewire\OrderConfirmation;
use App\Livewire\WelcomePage; // <-- TAMBAHKAN INI


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', WelcomePage::class);

Route::get('/order-confirmation/{pesananId}', OrderConfirmation::class)
    ->name('order.confirmation'); // Pastikan namanya persis 'order.confirmation'
