<?php

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

Auth::routes();

/*
 * Pages
 */
Route::get('/', 'MoviesController@movies')->name('movies');
Route::get('/watchlist', 'MoviesController@watchlist')->name('watchlist');
Route::view('/about', 'about')->name('about');
Route::get('/settings', 'SettingsController@index')->name('settings');
Route::post('/settings/change/email', 'SettingsController@changeEmail')->name('settings.change.email');
Route::post('/settings/change/password', 'SettingsController@changePassword')->name('settings.change.password');
Route::get('/statistics', 'StatisticsController@index')->name('statistics');

/*
 * API
 */
Route::middleware('check.ajax')->group(function () {
    Route::post('/api/add', 'MoviesController@add')->name('movies.add');
    Route::middleware('check.movie')->group(function () {
        Route::get('/api/get/{id}', 'MoviesController@get')->name('movies.get');
        Route::post('/api/remove', 'MoviesController@remove')->name('movies.remove');
        Route::post('/api/watched', 'MoviesController@markAsWatched')->name('movies.markAsWatched');
    });
    Route::post('/api/filter', 'MoviesController@filter')->name('movies.filter');
});