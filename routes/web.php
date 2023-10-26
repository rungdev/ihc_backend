<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\Dashboard;
use App\Http\Controllers\Display\Shelf;
use App\Http\Controllers\Stock\Order;
use App\Http\Controllers\Orders\Orderlist;
use App\Http\Controllers\Product\Brand;
use App\Http\Controllers\Product\Category;
use App\Http\Controllers\Product\Option;
use App\Http\Controllers\Product\Product;
use App\Http\Controllers\Product\Supplier;
use App\Http\Controllers\user\Signin;
use App\Http\Controllers\user\User;

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

Route::get('/signin', [Signin::class, 'signin'])->name('signin');
Route::post('/checkSignin', [Signin::class, 'checkSignin']);

Route::middleware(['chksignin'])->group(function () {
    Route::get('/', [Dashboard::class, 'dashboard']);
    Route::get('/dashboard', [Dashboard::class, 'dashboard']);
    Route::get('/orderlist', [Orderlist::class, 'orderlist']);
    Route::get('/orderview/{id}', [Orderlist::class, 'orderview']);
    Route::get('/usergroup', [User::class, 'usergroup']);
    Route::get('/formgroup', [User::class, 'formgroup']);
    Route::get('/formgroup/{id}', [User::class, 'formgroup']);
    Route::get('/users', [User::class, 'users']);
    Route::get('/formuser', [User::class, 'formuser']);
    Route::get('/formuser/{id}', [User::class, 'formuser']);
    Route::get('/printOrder/{id}', [Orderlist::class, 'printOrder']);
    Route::get('/product', [Product::class, 'productlist']);
    Route::get('/productCreate', [Product::class, 'productCreate']);
    Route::get('/productEdit/{id}', [Product::class, 'productEdit']);
    Route::get('/option', [Option::class, 'option']);
    Route::get('/brand', [Brand::class, 'brand']);
    Route::get('/category', [Category::class, 'category']);
    Route::get('/supplier', [Supplier::class, 'supplier']);
    Route::get('/supplierCreate', [Supplier::class, 'supplierView']);
    Route::get('/supplierEdit/{id}', [Supplier::class, 'supplierView']);
});


Route::controller(Product::class)
->middleware(['chksignin'])
->prefix('product')
->group(function() {
    Route::get('index', 'productlist')->name('index');
    Route::get('create', 'productCreate')->name('create');
    Route::get('edit/{id}', 'productEdit')->name('edit');

    Route::get('option', 'getProductOption')->name('option');
    Route::post('store', 'createProduct')->name('store');
    Route::post('update', 'updateProduct')->name('update');
    Route::post('get', 'productTable')->name('get');
    Route::post('imgtemp', 'imageProductTemp')->name('imgtemp');
    Route::post('updatestatus', 'productStatus')->name('updatestatus');
    Route::get('filtersubById/{id}', 'filtersubById')->name('filtersubById');
    Route::post('uploadimagemce', 'uploadimagemce')->name('uploadimagemce');

});

Route::controller(Shelf::class)
->middleware(['chksignin'])
->prefix('shelf')
->group(function() {
    Route::get('index', 'index')->name('index');
    Route::get('create', 'create')->name('create');
    Route::get('edit/{id}', 'edit')->name('edit');

    Route::get('table', 'table')->name('table');

});







Route::prefix('calldata')->group(function (){
    Route::post('/optionList', [Option::class, 'optionList']);
    Route::post('/optionById', [Option::class, 'optionById']);
    Route::post('/saveOption', [Option::class, 'saveOption']);
    Route::post('/optionStatus', [Option::class, 'optionStatus']);
    Route::post('/brandList', [Brand::class, 'brandList']);
    Route::post('/brandById', [Brand::class, 'brandById']);
    Route::post('/saveBrand', [Brand::class, 'saveBrand']);
    Route::post('/brandStatus', [Brand::class, 'brandStatus']);
    Route::post('/categoryById', [Category::class, 'categoryById']);
    Route::post('/saveCategory', [Category::class, 'saveCategory']);
    Route::post('/categoryStatus', [Category::class, 'categoryStatus']);
    Route::post('/searchCategory', [Category::class, 'searchCategory']);
    Route::post('/saveSupplier', [Supplier::class, 'saveSupplier']);
    Route::post('/supplierList', [Supplier::class, 'supplierList']);
    Route::post('/supplierStatus', [Supplier::class, 'supplierStatus']);
    // Route::post('/productStatus', [Product::class, 'productStatus']);
    // Route::get('/filtersubById/{id}', [Product::class, 'filtersubById']);
});