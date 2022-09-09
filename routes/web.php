<?php
    use Ships\Router;
    require_once(__DIR__ . '../../vendor/autoload.php');
    include(__DIR__ . '../../App/Handlers/CustomExceptionHandler.php');

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, "../.env");
    $dotenv->safeLoad();

    
    Router::group(['exceptionHandler' => \Ships\Handlers\CustomExceptionHandler::class], function () {

        Router::get($_ENV["ROUTE_MAIN"], 'DefaultController@home')->name('home');
        
        Router::post($_ENV["ROUTE_MAIN"] . 'server/', 'ServiceController@register')->name('register');

        Router::post($_ENV["ROUTE_MAIN"] . 'auth/', 'ServiceController@authAction')->name('auth');
    });


    Router::group(['exceptionHandler' => \Ships\Handlers\CustomExceptionHandler::class], function () {

        Router::get($_ENV["ROUTE_MAIN"] . 'ships/{page}', 'ShipsController@getShipsAction')->name('ships');

        Router::get($_ENV["ROUTE_MAIN"] . 'ship/{id}', 'ShipsController@getShipAction')->name('ship');

        Router::post($_ENV["ROUTE_MAIN"] . 'ships/', 'ShipsController@postShipsAction')->name('createShip');

        Router::put($_ENV["ROUTE_MAIN"] . 'ships/', 'ShipsController@putShipsAction')->name('updateShip');

        Router::delete($_ENV["ROUTE_MAIN"] . 'ships/', 'ShipsController@deleteShipsAction')->name('deleteShip');
    });

    Router::group(['exceptionHandler' => \Ships\Handlers\CustomExceptionHandler::class], function () {

        Router::get($_ENV["ROUTE_MAIN"] . 'sales/{page}', 'SalesController@getSalesAction')->name('sales');

        Router::get($_ENV["ROUTE_MAIN"] . 'sale/{id}', 'SalesController@getSaleAction')->name('sale');

        Router::post($_ENV["ROUTE_MAIN"] . 'sales/', 'SalesController@postSalesAction')->name('createShip');

        Router::put($_ENV["ROUTE_MAIN"] . 'sales/', 'SalesController@putSalesAction')->name('updateSale');

        Router::delete($_ENV["ROUTE_MAIN"] . 'sales/', 'SalesController@deleteSalesAction')->name('deleteSales');
    });

    Router::group(['exceptionHandler' => \Ships\Handlers\CustomExceptionHandler::class], function () {

        Router::get($_ENV["ROUTE_MAIN"] . 'users/{page}', 'UsersController@getUsersAction')->name('users');

        Router::get($_ENV["ROUTE_MAIN"] . 'user/{id}', 'UsersController@getUserAction')->name('user');

        Router::post($_ENV["ROUTE_MAIN"] . "users/", 'UsersController@postUsersAction')->name('createUser');

        Router::put($_ENV["ROUTE_MAIN"] . 'users/', 'UsersController@putUsersAction')->name('updateUser');

        Router::delete($_ENV["ROUTE_MAIN"] . 'users/', 'UsersController@deleteUsersAction')->name('deleteUser');
    })

    
?>