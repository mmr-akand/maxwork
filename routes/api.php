<?php

use Illuminate\Http\Request;


Route::group(['prefix' => 'authentication'], function() {

	Route::post('/login', 'Api\AuthController@login');
	Route::post('/logout',  ['uses'=>'Api\AuthController@logout', 'middleware'=>'auth:api']);
});

	Route::get('/users', ['uses'=>'Api\UserController@index', 'middleware'=>'auth:api']);
	Route::post('/users', ['uses'=>'Api\UserController@store', 'middleware'=>['auth:api', 'permission']]);
	Route::patch('/users/{id}', ['uses'=>'Api\UserController@update', 'middleware'=>'auth:api']);
	Route::delete('/users/{id}', ['uses'=>'Api\UserController@destroy', 'middleware'=>['auth:api', 'permission']]);

	Route::get('/projects', ['uses'=>'Api\ProjectController@index', 'middleware'=>'auth:api']);
	Route::post('/projects', ['uses'=>'Api\ProjectController@store', 'middleware'=>'auth:api']);
	Route::patch('/projects/{id}', ['uses'=>'Api\ProjectController@update', 'middleware'=>'auth:api']);
	Route::delete('/projects/{id}', ['uses'=>'Api\ProjectController@destroy', 'middleware'=>'auth:api']);
