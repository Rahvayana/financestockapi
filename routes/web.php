<?php


use Illuminate\Support\Facades\Route;

$router->group(['prefix' => 'api'], function () use ($router) 
{
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
});
