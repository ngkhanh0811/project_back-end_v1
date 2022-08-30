<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front;
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

Route::get('/', [Front\HomeController::class, 'index']);


Route::prefix('shop')->group(function (){
    Route::get('/product/{id}', [Front\ShopController::class, 'show']);
    Route::post('/product/{id}', [Front\ShopController::class, 'postComment']);
    Route::get('/', [Front\ShopController::class, 'index']);

    Route::get('/{categoryName}', [Front\ShopController::class, 'category']);
});

Route::prefix('cart')->group(function (){
    Route::get('add/{id}', [Front\CartController::class, 'add']);
    Route::get('/', [Front\CartController::class, 'index']);
    Route::get('/delete/{rowId}',[Front\CartController::class, 'delete']);
    Route::get('/destroy',[Front\CartController::class, 'destroy']);
    Route::get('/update', [Front\CartController::class, 'update']);
});

Route::prefix('checkout')->group(function(){
    Route::get('/', [Front\CheckOutController::class, 'index']);
    Route::post('/', [Front\CheckOutController::class, 'addOrder']);
    Route::get('/vnPayCheck', [Front\CheckOutController::class, 'vnPayCheck']);
    Route::get('/result', [Front\CheckOutController::class, 'result']);
});
