<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Products\CategoryProductController;
use App\Http\Controllers\Api\Products\FeaturedProductController;
use App\Http\Controllers\Api\Products\RecentlyViewedProductController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MetadataController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PlaygroundTestController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClosetController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::post('cloudinary/image-upload-test', [PlaygroundTestController::class, 'uploadImageToCloudinary']);

Route::get('countries-meta-data', [MetadataController::class, 'getMetaData']);
Route::get('country-list', [MetadataController::class, 'getCountriesList']);

Route::get('meta-data', [HomeController::class, 'getMetaContent']);
Route::get('mega-menu', [HomeController::class, 'getMegaMenu']);


Route::post('login', [AuthController::class, "login"]);
Route::post('register', [AuthController::class, "register"]);

#Home Page
Route::get('homepage', [HomeController::class, 'getHomePageContent']);
Route::get('homepage/featured-section', [HomeController::class, 'getHomePageFeaturedContent']);

Route::post('/product/{handle}',  [ProductController::class, 'getProductDetail']);
#Featured Products
Route::get('/featured-products',  [FeaturedProductController::class, 'getFeaturedProducts']);
Route::post('/filter/featured-products', [FeaturedProductController::class, 'getFeaturedProducts']);
#Recently Viewed Products
Route::get('/recently-viewed-products',  [RecentlyViewedProductController::class, 'getRecentlyViewedProducts']);
Route::post('/filter/recently-viewed-products', [RecentlyViewedProductController::class, 'getCachedRecentlyViewedProducts']);
#categories
Route::get('/categories', [CategoryController::class, 'getCategories']);
Route::get('/categories/{slug}', [CategoryController::class, 'getSubCategories']);
#category products
Route::get('/categories/{slug}/products', [CategoryProductController::class, 'getProducts']);
Route::get('/filter/categories/{slug}/products', [CategoryProductController::class, 'getFilteredCategoryProducts']);


#Closet List
Route::get('/closets/get-all/{type}', [ClosetController::class, 'getAllClosets']);
#Closets
Route::get('/closet/{slug}', [ClosetController::class, 'getCloset']);
#Closet Products
Route::get('/closet/{slug}/product', [ClosetController::class, 'getClosetProducts']);
Route::get('/filter/closet/{slug}/product', [ClosetController::class, 'getFilteredClosetProducts']);
#Closet Category
Route::get('/closet/{slug}/category/{catSlug}', [ClosetController::class, 'getClosetCategory']);
Route::get('/closet/{slug}/category/{catSlug}/product', [ClosetController::class, 'getClosetCategoryProducts']);

Route::middleware(['tokenValidation', 'auth:api'])->group(function () {
    Route::post('verify-phone', [OtpController::class, "sendOtp"]);

});
