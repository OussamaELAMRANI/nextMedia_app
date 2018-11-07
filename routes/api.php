<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'auth'], function () {

	Route::post('login', 'Api\AuthController@login');
	Route::post('signup', 'Api\AuthController@signup');

	Route::group(['middleware' => 'auth:api'], function () {
		Route::get('logout', 'Api\AuthController@logout');
		Route::get('user', 'Api\AuthController@user');
	});
});

Route::group(['prefix' => 'product'], function () {

	Route::get('/available/{id}', 'Api\ProductController@show');
	Route::get('/available', 'Api\ProductController@index');

	Route::middleware(['auth:api', 'scope:seller'])->group(function () {

		Route::get('/', 'Api\ProductController@getSellerProducts');
		Route::get('/{slug}', 'Api\ProductController@getSellerProduct');
		Route::post('/new', 'Api\ProductController@store');
		Route::put('/{slug}/update', 'Api\ProductController@update');
		Route::delete('/{slug}/delete', 'Api\ProductController@delete');
	});


});
