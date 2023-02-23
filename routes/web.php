<?php

use Ships\Router;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, "../.env");
$dotenv->safeLoad();
$routepath = $_ENV["ROUTE_MAIN"];

/**
 * @Route("/")
 * @Middleware(\Ships\Middleware\DefaultMiddleware::class)
 * @Controller("ServicesController)
 */
Router::group(
  ["exceptionHandler" => \Ships\Handlers\CustomExceptionHandler::class],
  function () {
    Router::get($_ENV["ROUTE_MAIN"], "DefaultController@home")->name("home");

    // Register
    Router::post(
      $_ENV["ROUTE_MAIN"] . "register/",
      "ServicesController@registerAction"
    )->name("register");

    // Login and get token
    Router::post(
      $_ENV["ROUTE_MAIN"] . "auth/",
      "ServicesController@authAction"
    )->name("auth");
  }
);

/**
 * @Route("/ships")
 * @Middleware(\Ships\Middleware\ShipsMiddleware::class)
 */
Router::group(
  [
    "middleware" => \Ships\Middlewares\AuthMiddleware::class,
    "exceptionHandler" => \Ships\Handlers\CustomExceptionHandler::class,
  ],
  function () {
     /**
     * Load the entire controller (where url matches method names - getShipsAction() - getShipAction() ).
     * The url paths will determine which method to render.
     *
     * For example:
     *
     * GET  /ships/{page}        => getShipsAction($page)
     * GET  /ship/{id}           => getShipAction($id)
     * POST /ships/              => postShipsAction()
     * PUT  /ships/              => putShipsAction()
     * DELETE /ships/            => deleteShipsAction()
     */
    Router::get(
      $_ENV["ROUTE_MAIN"] . "ships/{page}",
      "ShipsController@getShipsAction"
    )->name("ships");

    Router::get(
      $_ENV["ROUTE_MAIN"] . "ship/{id}",
      "ShipsController@getShipAction"
    )->name("ship");

    Router::post(
      $_ENV["ROUTE_MAIN"] . "ships/",
      "ShipsController@postShipsAction"
    )->name("postShip");

    Router::put(
      $_ENV["ROUTE_MAIN"] . "ships/",
      "ShipsController@putShipsAction"
    )->name("putShip");

    Router::delete(
      $_ENV["ROUTE_MAIN"] . "ships/{id}",
      "ShipsController@deleteShipsAction"
    )->name("deleteShip");
  }
);

Router::group(
  ["exceptionHandler" => \Ships\Handlers\CustomExceptionHandler::class],
  function () {
    Router::get(
      $_ENV["ROUTE_MAIN"] . "sales/{page}",
      "SalesController@getSalesAction"
    )->name("sales");

    Router::get(
      $_ENV["ROUTE_MAIN"] . "sale/{id}",
      "SalesController@getSaleAction"
    )->name("sale");

    Router::post(
      $_ENV["ROUTE_MAIN"] . "sales/",
      "SalesController@postSalesAction"
    )->name("createShip");

    Router::put(
      $_ENV["ROUTE_MAIN"] . "sales/",
      "SalesController@putSalesAction"
    )->name("updateSale");

    Router::delete(
      $_ENV["ROUTE_MAIN"] . "sales/",
      "SalesController@deleteSalesAction"
    )->name("deleteSales");
  }
);

Router::group(
  ["exceptionHandler" => \Ships\Handlers\CustomExceptionHandler::class],
  function () {
    Router::get(
      $_ENV["ROUTE_MAIN"] . "users/{page}",
      "UsersController@getUsersAction"
    )->name("users");

    Router::get(
      $_ENV["ROUTE_MAIN"] . "user/{id}",
      "UsersController@getUserAction"
    )->name("user");

    Router::post(
      $_ENV["ROUTE_MAIN"] . "users/",
      "UsersController@postUsersAction"
    )->name("createUser");

    Router::put(
      $_ENV["ROUTE_MAIN"] . "users/",
      "UsersController@putUsersAction"
    )->name("updateUser");

    Router::delete(
      $_ENV["ROUTE_MAIN"] . "users/",
      "UsersController@deleteUsersAction"
    )->name("deleteUser");
  }
);

//New route home like

?>
