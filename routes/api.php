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

Route::post('/register', 'Auth\RegisterController@register');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');
Route::post('/check-email', 'Auth\RegisterController@checkEmail');

Route::group([
    'prefix'=>'lists',
    'as'=>'lists'
], function(){
    Route::post('/create', 'ReadingListController@store');
    Route::delete('/delete/{id}', 'ReadingListController@destroy');
    Route::put('/edit/{readingList}', 'ReadingListController@edit');
    Route::get('/get', 'ReadingListController@get');
    Route::put('/reorder', 'ReadingListController@reorderList');
    Route::put('/reorder-multiple', 'ReadingListController@reorderMultipleLists');
});

Route::group([
    'prefix'=>'link',
    'as'=>'link'
], function(){
    Route::get('/archives', 'LinkController@getArchives');
    Route::post('/create', 'LinkController@store');
    Route::post('/archive/{link}', 'LinkController@archive');
    Route::post('/delete/{id}', 'LinkController@delete');
    Route::put('/edit/{link}', 'LinkController@rename');
    Route::put('/move/{link}', 'LinkController@move');
    Route::put('/reorder', 'LinkController@reorderLinks');
    Route::put('/restore/{id}', 'LinkController@restore');
});
