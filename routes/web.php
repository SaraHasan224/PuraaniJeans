<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/login', 'UserController@login')->name('users.login');
Route::get('/forget-password', 'UserController@forgetPassword')->name('users.forget_password');


Route::get('/', 'HomeController@index')->name('home');

Route::get('/users', 'UserController@index')->name('users');
Route::get('/roles', 'RoleController@index')->name('roles');

Route::get('/closet', 'Closet\OrderController@index')->name('closet');
Route::get('/closet/orders', 'Closet\OrderController@index')->name('closet-orders');
Route::get('/closet/pim', 'Closet\PIMController@index')->name('closet-pim');

Route::get('/customers', 'CustomerController@index')->name('customers');

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/welcome', function () {
    return view('welcome');
});


