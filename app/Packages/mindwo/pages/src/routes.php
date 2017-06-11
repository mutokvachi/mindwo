<?php

// Raksti atbilstoši iezīmēm
Route::get('/raksti_{id}', array('as' => 'tag_articles', 'middleware' => 'public', 'uses'=>'mindwo\pages\Controllers\ArticlesController@showTagArticles'));
Route::post('/raksti_{id}', array('as' => 'tag_articles', 'middleware' => 'public', 'uses'=>'mindwo\pages\Controllers\ArticlesController@showTagArticles'));
Route::get('/datu_avota_raksti_{id}', array('as' => 'tag_articles', 'middleware' => 'public', 'uses'=>'mindwo\pages\Controllers\ArticlesController@showSourceArticles'));
Route::post('/datu_avota_raksti_{id}', array('as' => 'tag_articles', 'middleware' => 'public', 'uses'=>'mindwo\pages\Controllers\ArticlesController@showSourceArticles'));

// Attēli
Route::get('/text_img/{file}/{text}','mindwo\pages\Controllers\ImageController@getImageText');

// Bloku AJAX pieprasījumi
Route::post('/block_ajax', array('as' => 'block_ajax',  'middleware' => 'public_ajax', 'uses'=>'mindwo\pages\Controllers\BlockAjaxController@getData'));

// Kalendāra ieraksti
Route::post('/event', array('as' => 'event',  'middleware' => 'public_ajax', 'uses'=>'mindwo\pages\Controllers\CalendarController@getEvent'));

// Lapas
Route::get('/{id}/{item}', array('as' => 'mindwo',  'middleware' => 'public', 'uses'=>'mindwo\pages\Controllers\PagesController@showPageItem'));
Route::get('/{id}', array('as' => 'mindwo',  'middleware' => 'public', 'uses'=>'mindwo\pages\Controllers\PagesController@showPage'));
Route::post('/{id}', array('as' => 'mindwo',  'middleware' => 'public', 'uses'=>'mindwo\pages\Controllers\PagesController@showPage'));

// Noklusētā lapa
Route::get('/', array('as' => 'home', 'middleware' => 'public', 'uses'=>'mindwo\pages\Controllers\PagesController@showRoot'));