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
Route::post('/check-email', 'Auth\RegisterController@checkEmail')->name('check-email');
Route::post('/home', 'PageController@home')->name('home');

Route::group([
    'prefix'=>'lists',
    'as'=>'lists.'
], function(){
    Route::post('/create', 'ReadingListController@store')->name('create');
    Route::delete('/delete/{id}', 'ReadingListController@delete')->name('delete');
    Route::put('/edit/{readingList}', 'ReadingListController@edit')->name('edit');
    Route::get('/get', 'ReadingListController@get')->name('get');
    Route::put('/reorder', 'ReadingListController@reorderList')->name('reorder');
    Route::put('/reorder-multiple', 'ReadingListController@reorderMultipleLists')->name('reorder-multiple');
});

Route::group([
    'prefix'=>'link',
    'as'=>'link.'
], function(){
    Route::get('/archives/{user}', 'LinkController@getArchives')->name('archives');
    Route::post('/create', 'LinkController@store')->name('create');
    Route::post('/archive/{link}', 'LinkController@archive')->name('archive');
    Route::post('/delete/{id}', 'LinkController@delete')->name('delete');
    Route::put('/edit/{link}', 'LinkController@rename')->name('edit');
    Route::put('/move/{link}', 'LinkController@move')->name('move');
    Route::put('/reorder', 'LinkController@reorderLinks')->name('reorder');
    Route::put('/restore/{id}', 'LinkController@restore')->name('restore');
});
