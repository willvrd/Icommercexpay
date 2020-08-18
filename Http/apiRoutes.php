<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => 'icommercexpay'], function (Router $router) {
    
    $router->get('/', [
        'as' => 'icommercexpay.api.checkmo.init',
        'uses' => 'IcommerceXpayApiController@init',
    ]);

    $router->get('/response', [
        'as' => 'icommercexpay.api.checkmo.response',
        'uses' => 'IcommerceXpayApiController@response',
    ]);

});