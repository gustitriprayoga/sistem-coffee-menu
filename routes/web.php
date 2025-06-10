<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\CheckoutForm;
use App\Livewire\CheckoutForm as LivewireCheckoutForm;
use App\Livewire\HomePage;
use App\Http\Livewire\MenuList;
use App\Livewire\Checkout;
use App\Livewire\Home;
use App\Livewire\Keranjang;
use App\Livewire\KeranjangPage;
use App\Livewire\MenuList as LivewireMenuList;
use App\Livewire\PesananPage;
use App\Livewire\Pesanan;
use App\Livewire\PesananSaya;

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



Route::get('/', Home::class)->name('home');
Route::get('/keranjang', Keranjang::class)->name('keranjang');

Route::get('/checkout', Checkout::class)->name('checkout');

Route::get('/pesanan', Pesanan::class)->name('pesanan')->middleware('auth');
Route::get('/pesanan', PesananSaya::class)->name('pesanan');
