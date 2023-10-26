<?php

use App\Http\Controllers\Branch\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\Orderlist;
use App\Http\Controllers\Product\Product;
use App\Http\Controllers\user\Signin;
use App\Http\Controllers\user\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('backoffice')->group(function (){
    Route::get('/orderLists', [Orderlist::class, 'getOrderList']);
    Route::put('/saveTracking', [Orderlist::class, 'saveTracking']);
    Route::post('/usergroup', [User::class, 'getListUser']);
    Route::patch('/saveGroup', [User::class, 'saveGroup']);
    Route::put('/groupStatus', [User::class, 'groupStatus']);
    Route::post('/getUserlist', [User::class, 'getUserlist']);
    Route::put('/activeUser', [User::class, 'activeUser']);
    Route::post('/userSave', [User::class, 'userSave']);
    Route::patch('/deposit_advance', [Orderlist::class, 'deposit_advance']);
    Route::patch('/deposit_advance', [Orderlist::class, 'deposit_advance']);
    Route::patch('/note_internal', [Orderlist::class, 'note_internal']);
    Route::patch('/update_status_order', [Orderlist::class, 'update_status_order']);
    Route::post('/productTable', [Product::class, 'productTable']);
    Route::get('/getProductOption', [Product::class, 'getProductOption']);
    Route::post('/imageProductTemp', [Product::class, 'imageProductTemp']);
    Route::post('/createProduct', [Product::class, 'createProduct']);
    Route::get('/branch', [Branch::class, 'branch']);
    Route::post('/updateProduct', [Product::class, 'updateProduct']);
});

