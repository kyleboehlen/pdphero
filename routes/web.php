<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth
Auth::routes();

// Root route, controls whether or not user gets sent to about or home
Route::get('/', [HomeController::class, 'index'])->name('root');

// Main about page
Route::get('about', [AboutController::class, 'index'])->name('about');

