<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
    Route::group(['as' => 'api.v1.'], function () {
        Route::post('login', 'Auth\LoginController@login');
        Route::get('forgot-password', 'Auth\ForgotPasswordController@forgotPassword');
        Route::put('reset-password', 'Auth\ResetPasswordController@resetPassword');

        Route::get('login-google', 'Auth\LoginController@loginGoogle');

        Route::middleware(['auth.jwt'])->group(function () {
            Route::get('logout', 'Auth\LoginController@logout');
            Route::get('info', 'Auth\LoginController@getInfo');

            Route::group(['prefix' => 'users'], function () {
                Route::get('/', 'UserController@index');
                Route::get('list-user', 'UserController@listUser');
                Route::get('list-admin', 'UserController@listAdmin');
            });
            
            Route::get('categories', 'CategoryController@index');
            
            Route::get('departments', 'DepartmentController@index');
            
            Route::group(['prefix' => 'requests'], function () {
                Route::get('/', 'RequestController@index');
                Route::get('show/{id}', 'RequestController@show');
                Route::get('show-my-request', 'RequestController@showMyRequest');
                Route::post('create', 'RequestController@create');
                Route::put('update/{id}', 'RequestController@update');
                Route::delete('destroy/{id}', 'RequestController@delete');
                Route::put('change-status/{id}', 'RequestController@changeStatus');
            });

            Route::group(['prefix' => 'commenthistory'], function () {
                Route::get('/', 'CommentHistoryController@index');
            });

            Route::group(['prefix' => 'comment'], function () {
                Route::get('/', 'CommentController@index');
                Route::get('list-comment/{id}', 'CommentController@listComment');
                Route::post('create', 'CommentController@create');
            });

            Route::middleware(['admin.jwt'])->group(function () {
                Route::group(['prefix' => 'categories'], function () {
                    Route::get('show/{id}', 'CategoryController@show');
                    Route::post('create', 'CategoryController@create');
                    Route::put('update/{id}', 'CategoryController@update');
                    Route::delete('destroy/{id}', 'CategoryController@delete');
                });

                Route::group(['prefix' => 'departments'], function () {
                    Route::get('show/{id}', 'DepartmentController@show');
                    Route::post('create', 'DepartmentController@create');
                    Route::put('update/{id}', 'DepartmentController@update');
                });

                Route::group(['prefix' => 'users'], function () {
                    Route::post('create', 'UserController@create');
                    Route::put('update/{id}', 'UserController@update');
                    Route::get('show/{id}', 'UserController@show');
                });
            });
        });
    });
});
