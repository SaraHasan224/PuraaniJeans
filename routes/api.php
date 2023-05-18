<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login', 'Api\AuthController@login');
Route::post('register', 'Api\AuthController@register');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

#Featured Products
Route::get('/featured-products', 'ProductController@getFeaturedProducts');
Route::post('/filter/featured-products', 'ProductController@getFilteredFeaturedProducts');

#categories
Route::get('/categories', 'CategoryController@getCategories');
Route::get('/categories/{slug}', 'CategoryController@getSubCategories');
Route::get('/categories/{slug}/products', 'CategoryController@getProducts');
Route::post('/filter/categories/{slug}/products', 'CategoryController@getFilteredCategoryProducts');


#Closet List
Route::get('/stores/list/{type}', 'StoresController@getAllStores');
#Closets
Route::get('/stores/{slug}', 'StoresController@getStore');
Route::get('/stores/{slug}/product', 'StoresController@getStoreProducts');
Route::post('/filter/stores/{slug}/product', 'StoresController@getFilteredStoreProducts');
#Closet Category
Route::get('/stores/{slug}/category/{catSlug}', 'StoresController@getStoreCategory');
Route::get('/stores/{slug}/category/{catSlug}/product', 'StoresController@getStoreCategoryProducts');
