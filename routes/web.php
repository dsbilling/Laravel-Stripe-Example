<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [
    'as' => 'home' ,
    'uses' => 'HomeController@index'
]);

Route::get('/card', [
    'as' => 'card' ,
    'uses' => 'CardController@index'
]);
Route::get('/card/create', [
    'as' => 'card-create' ,
    'uses' => 'CardController@create'
]);
Route::post('/card/store', [
    'as' => 'card-store' ,
    'uses' => 'CardController@store'
]);
Route::get('/card/{id}/destroy', [
    'as' => 'card-destroy' ,
    'uses' => 'CardController@destroy'
]);