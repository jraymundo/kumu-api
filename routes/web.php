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

$router->group([
    'namespace' => 'Api',
    'prefix' => 'v1',
    'middleware' => ['json'],
],
    function () use ($router) {
        $router->post('auth/register', 'AuthController@register');
        $router->post('auth/login', 'AuthController@login');
    });

$router->group([
    'namespace' => 'Api',
    'prefix' => 'v1',
    'middleware' => ['jwt', 'json'],
],
    function () use ($router) {
        $router->post('github/users', 'GithubController@getUsers');
    });
