<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

function setRoutes($router, $name) {
    $router->group(['prefix' => 'api/v1/' . strtolower($name)], function() use ($router, $name) {
        $router->get('/', $name . "Controller@get");
        $router->post('/', $name . 'Controller@create');
        $router->get('/{id}', $name . 'Controller@getById');
        $router->put('/{id}', $name . 'Controller@update');
        $router->delete('/{id}', $name . 'Controller@delete');
    });
}

$routes = ['customers', 'users'];
foreach ($routes as $name) {
    setRoutes($router, $name);
}

$router->group(['prefix' => 'api/v1/account'], function() use ($router) {
    $router->post("/login", 'AuthenticationController@login');
    
});

$router->group(['prefix' => 'api/v1/users'], function() use ($router) {
    $router->get("/email/{email}", 'UsersController@getByEmail');
});