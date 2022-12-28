<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\HomeController;

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
Route::get('/',[HomeController::class,'index'])->name('index');
Route::post('/',[HomeController::class,'storeImage'])->name('storeImage');

Route::get('image',[HomeController::class,'image'])->name('image');
Route::post('image', [HomeController::class, 'getImage'])->name('showImage');

Route::get('keys',[HomeController::class,'keys'])->name('keys');

// Route::get('auto-scale', [HomeController::class, 'autoScalling'])->name('autoScalling');

Route::prefix('admin')->middleware('auth')->group(function(){
    Route::get('cache-config',[HomeController::class,'cacheConfig'])->name('cache-config');
    Route::post('cache-config',[HomeController::class,'storeCacheConfig'])->name('storeCacheConfig');

    Route::get('cache-status', [HomeController::class, 'statistics'])->name('cacheStatus');
    // Route::post('cache-status', [HomeController::class, 'storeCacheStatus'])->name('storeCacheStatus');

    Route::get('cache-clear', [HomeController::class, 'clearCache'])->name('clearCache');

    Route::get('pool-resizing', [HomeController::class, 'poolResizing'])->name('poolResizing');

    Route::post('increase-pool', [HomeController::class, 'increasePools'])->name('increasePools');
    Route::post('decrease-pool', [HomeController::class, 'decreasePools'])->name('decreasePools');

    Route::post('cache-status', [HomeController::class, 'storeCacheStatus'])->name('storeCacheStatus');

    // Route::get('auto-scale', [HomeController::class, 'scale'])->name('scale');
    Route::post('auto-scale', [HomeController::class, 'autoScalling'])->name('autoScalling');
});

Route::get('test', function(){
    dd(session()->get('cache'));
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
