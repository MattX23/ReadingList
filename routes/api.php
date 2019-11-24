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
    'as'=>'lists'
], function(){
    Route::post('/create', 'ReadingListController@store')->name('list.create');
    Route::delete('/delete/{id}', 'ReadingListController@destroy')->name('list.delete');
    Route::put('/edit/{readingList}', 'ReadingListController@edit')->name('list.edit');
    Route::get('/get', 'ReadingListController@get')->name('list.get');
    Route::put('/reorder', 'ReadingListController@reorderList')->name('list.reorder');
    Route::put('/reorder-multiple', 'ReadingListController@reorderMultipleLists')->name('list.reorder-multiple');
});

Route::group([
    'prefix'=>'link',
    'as'=>'link'
], function(){
    Route::get('/archives', 'LinkController@getArchives')->name('link.archives');
    Route::post('/create', 'LinkController@store')->name('link.create');
    Route::post('/archive/{link}', 'LinkController@archive')->name('link.archive-link');
    Route::post('/delete/{id}', 'LinkController@delete')->name('link.delete');
    Route::put('/edit/{link}', 'LinkController@rename')->name('link.edit');
    Route::put('/move/{link}', 'LinkController@move')->name('link.move');
    Route::put('/reorder', 'LinkController@reorderLinks')->name('link.reorder');
    Route::put('/restore/{id}', 'LinkController@restore')->name('link.restore');
});
