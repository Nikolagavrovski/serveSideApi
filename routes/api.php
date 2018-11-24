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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Test


//Authentication and registertion
Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');
Route::middleware('auth:api')->post('/logout', 'AuthController@logout');

// Get all contacts 
Route::get('/contacts/{id}','ContactsController@getContacts');

// Get converstion by ID
Route::get('/conversation/{id}', 'ContactsController@getMessagesById');

// Send conversation
Route::post('/conversation/send', 'ContactsController@send');

Route::get('/test', 'ContactsController@test');