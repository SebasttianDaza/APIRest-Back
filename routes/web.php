<?php
    use Ships\Router;
    include(__DIR__ . '../../App/Handlers/CustomExceptionHandler.php');
    
    Router::group(['exceptionHandler' => \Ships\Handlers\CustomExceptionHandler::class], function () {

        Router::get('APIRest-Back/', 'DefaultController@home')->name('home');
        
        Router::post('APIRest-Back/server/', 'ServiceController@register')->name('register');

        Router::post('APIRest-Back/auth/', 'ServiceController@authAction')->name('auth');
    });


    Router::group(['exceptionHandler' => \Ships\Handlers\CustomExceptionHandler::class], function () {

        Router::get('APIRest-Back/ships/{page}', 'ShipsController@getShipsAction')->name('ships');

        Router::get('APIRest-Back/ship/{id}', 'ShipsController@getShipAction')->name('ship');

        Router::post('APIRest-Back/ships/', 'ShipsController@postShipsAction')->name('createShip');

        Router::put('APIRest-Back/ships/', 'ShipsController@putShipsAction')->name('updateShip');

        Router::delete('APIRest-Back/ships/', 'ShipsController@deleteShipsAction')->name('deleteShip');
    });
    
?>