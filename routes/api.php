<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', 'Auth\RegisterController@register')->name('register');
Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/user', function() {
    return Auth::user();
})->name('user');

Route::resource('/books', 'BookController');
Route::get('/books/user/{user_id}', 'BookController@usersBook');
Route::put('/requests/{book_id}', 'BookController@addRequest');

Route::resource('/requests', 'RequestController');

// Route::get('/refresh-token', function (Illuminate\Http\Request $request) {
//     $request->session()->regenerateToken();
//     return response()->json();
// });
