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
