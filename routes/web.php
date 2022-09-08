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

    Router::group(['exceptionHandler' => \Ships\Handlers\CustomExceptionHandler::class], function () {

        Router::get('APIRest-Back/sales/{page}', 'ShipsController@getSalesAction')->name('sales');

        Router::get('APIRest-Back/sale/{id}', 'ShipsController@getSaleAction')->name('sale');

        Router::post('APIRest-Back/sales/', 'ShipsController@postSalesAction')->name('createShip');

        Router::put('APIRest-Back/sales/', 'ShipsController@putSalesAction')->name('updateSale');

        Router::delete('APIRest-Back/sales/', 'ShipsController@deleteSalesAction')->name('deleteSales');
    });

    Router::group(['exceptionHandler' => \Ships\Handlers\CustomExceptionHandler::class], function () {

        Router::get('APIRest-Back/users/{page}', 'UsersController@getUsersAction')->name('users');

        Router::get('APIRest-Back/user/{id}', 'UsersController@getUserAction')->name('user');

        Router::post("APIRest-Back/users/", 'UsersController@postUsersAction')->name('createUser');

        Router::put('APIRest-Back/users/', 'UsersController@putUsersAction')->name('updateUser');

        Router::delete('APIRest-Back/users/', 'UsersController@deleteUsersAction')->name('deleteUser');
    })

    
?>