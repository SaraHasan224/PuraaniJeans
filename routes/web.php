<?php

use App\Http\Controllers\Web\CountryController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\Closet\OrderController;
use App\Http\Controllers\Web\Closet\PIMController;

use App\Http\Controllers\Web\Auth\PasswordController;
use App\Http\Controllers\Web\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Web\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Web\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Web\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Web\Auth\NewPasswordController;
use App\Http\Controllers\Web\Auth\PasswordResetLinkController;
use App\Http\Controllers\Web\Auth\RegisteredUserController;
use App\Http\Controllers\Web\Auth\VerifyEmailController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

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

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    return 'DONE'; //Return anything
});

Route::get('/test-mail', function () {
    Mail::send(new \App\Mail\UserCreated());
});
Route::get('/get-active-countries/{countryCode?}', [CountryController::class, 'getActiveCountries'])->name('active.countries');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('signin', [AuthenticatedSessionController::class, 'store'])->name('signin');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard'); //completed

    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    //User Management
//    Route::resources([
//        'users' => UserController::class,
//    ]);
    Route::get('/users', [UserController::class, 'index'])->name('users'); //completed
    Route::get('/users-create', [UserController::class, 'create'])->name('users.create'); //completed
    Route::post('/user-save', [UserController::class, 'store'])->name('users.store'); //completed
    Route::get('/users-list', [UserController::class, 'getListingRecord'])->name('users-list');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('user-edit-form');
    Route::post('/users/edit/{id}', [UserController::class, 'update'])->name('user-edit');
    Route::post('/users-delete', [UserController::class, 'deleteRecords'])->name('users-delete');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
    Route::get('/closet', [OrderController::class, 'index'])->name('closet');
    Route::get('/closet/orders', [OrderController::class, 'index'])->name('closet-orders');
    Route::get('/closet/pim', [PIMController::class, 'index'])->name('closet-pim');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');



    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
