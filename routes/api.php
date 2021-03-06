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
    Route::delete('/delete/{readingList}', 'ReadingListController@archive')->name('delete')->middleware('can:archive,readingList');

});

Route::group([
    'prefix'=>'link',
    'as'=>'link.'
], function(){
    Route::get('/archives', 'ArchiveController@getArchives')->name('archives');
    Route::post('/create', 'LinkController@store')->name('create');
    Route::put('/reorder', 'LinkController@reorderLinks')->name('reorder');

    Route::post('/archive/{link}', 'LinkController@archive')->name('archive')->middleware('can:delete,link');
    Route::delete('/delete/{link}', 'LinkController@deletePermanently')->name('delete')->middleware('can:deletePermanently,link');
    Route::put('/edit/{link}', 'LinkController@edit')->name('edit')->middleware('can:edit,link');
    Route::delete('/force-delete/{archive}', 'ArchiveController@delete')->name('deleteArchive')->middleware('can:delete,archive');
    Route::put('/restore/{archive}', 'ArchiveController@restore')->name('restore')->middleware('can:restore,archive');
});
