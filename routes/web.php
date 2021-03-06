<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

    $router->group(['prefix' => 'api/v1'], function () use($router){
        
        $router->post('/customer', 'CustomerController@create');
        $router->get('/customer', 'CustomerController@index');
        $router->get('/customer/{id}', 'CustomerController@find');
        $router->put('/customer/{id}', 'CustomerController@update');
        $router->delete('/customer/{id}', 'CustomerController@delete');
        $router->put('/change_password', 'CustomerController@changePassword');
        $router->post('/reset_password', 'CustomerController@resetPassword');
        $router->post('/verify_token/{token}', 'CustomerController@retriveToken');
        $router->get('/verify/{token}', 'CustomerController@verifyRegister');
        

        $router->post('/order', 'OrderController@create');
        $router->get('/order', 'OrderController@index');
        $router->get('/order/{id}', 'OrderController@find');
        $router->put('/order/{id}', 'OrderController@update');
        $router->delete('/order/{id}', 'OrderController@delete');

        $router->post('/product', 'ProductController@create');
        $router->get('/product', 'ProductController@index');
        $router->get('/product/{id}', 'ProductController@find');
        $router->put('/product/{id}', 'ProductController@update');
        $router->delete('/product/{id}', 'ProductController@delete');

        $router->post('/payment', 'PaymentController@create');
        $router->get('/payment', 'PaymentController@index');
        $router->get('/payment/{id}', 'PaymentController@find');
        $router->put('/payment/{id}', 'PaymentController@update');
        $router->delete('/payment/{id}', 'PaymentController@delete');

        $router->post('/login', 'UserController@login');

        $router->post('/payment/midtrans/push', 'PaymentController@pushNotif');

    });