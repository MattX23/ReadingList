<?php

Route::get('/', 'PageController@home')->name('home');
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
    Route::get('/get', 'ReadingListController@get')->name('get');
    Route::put('/reorder', 'ReadingListController@reorderList')->name('reorder');
    Route::put('/reorder-multiple', 'ReadingListController@reorderMultipleLists')->name('reorder-multiple');

    Route::put('/edit/{readingList}', 'ReadingListController@edit')->name('edit')->middleware('can:edit,readingList');

    // Handled via AuthorizeSoftDeletesTrait

    Route::delete('/delete/{id}', 'ReadingListController@delete')->name('delete');
});

Route::group([
    'prefix'=>'link',
    'as'=>'link.'
], function(){
    Route::post('/create', 'LinkController@store')->name('create');
    Route::put('/reorder', 'LinkController@reorderLinks')->name('reorder');

    Route::post('/archive/{link}', 'LinkController@archive')->name('archive')->middleware('can:archive,link');
    Route::post('/delete/{link}', 'LinkController@delete')->name('delete')->middleware('can:delete,link');
    Route::put('/edit/{link}', 'LinkController@edit')->name('edit')->middleware('can:edit,link');

    // Handled via AuthorizeSoftDeletesTrait

    Route::get('/archives/{user}', 'LinkController@getArchives')->name('archives');
    Route::post('/force-delete/{id}', 'LinkController@deleteFromArchives')->name('deleteFromArchives');
    Route::put('/restore/{id}', 'LinkController@restore')->name('restore');
});
