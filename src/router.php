<?php

namespace Ships;

use Pecee\SimpleRouter\SimpleRouter;
use Dotenv;

class Router extends SimpleRouter
{
  /**
   * @throws \Exception
   * @throws \Pecee\Http\Middleware\Exceptions\TokenMismatchException
   * @throws \Pecee\SimpleRouter\Exceptions\HttpException
   * @throws \Pecee\SimpleRouter\Exceptions\NotFoundHttpException
   */
  public static function start(): void
  {
    $root = $_SERVER["DOCUMENT_ROOT"];
    // Load env variables
    self::loadEnv($root);
    // Load our helpers
    self::loadHelpers($root);
    // Load our middlewares
    self::loadMiddlewarres($root);
    // Load our handlers
    self::loadHandlerException($root);
    // Load our controllers
    self::loadControllers($root);
    // Set namespace
    parent::setDefaultNamespace("Ships\Controllers");
    // Load our custom routes
    self::loadCustomRoutes($root);

    parent::enableMultiRouteRendering(false);

    // Do initial stuff
    parent::start();
  }

  /**
   * @throws \Exception
   * @throws \Pecee\Http\Middleware\Exceptions\TokenMismatchException
   * @throws \Pecee\SimpleRouter\Exceptions\HttpException
   * @throws \Pecee\SimpleRouter\Exceptions\NotFoundHttpException
   */
  public static function loadEnv(string $root): void
  {
    $dotenv = Dotenv\Dotenv::createImmutable($root, "/.env");
    $dotenv->safeLoad();
  }

  /**
   * @throws \Exception
   * @throws \Pecee\Http\Middleware\Exceptions\TokenMismatchException
   * @throws \Pecee\SimpleRouter\Exceptions\HttpException
   * @throws \Pecee\SimpleRouter\Exceptions\NotFoundHttpException
   */
  public static function loadHelpers(string $root): void
  {
    require_once $root . "/src/helpers/helpers.php";
  }

  /**
   * @throws \Exception
   * @throws \Pecee\Http\Middleware\Exceptions\TokenMismatchException
   * @throws \Pecee\SimpleRouter\Exceptions\HttpException
   * @throws \Pecee\SimpleRouter\Exceptions\NotFoundHttpException
   */
  public static function loadMiddlewarres(string $root): void
  {
    require_once $root . "/app/Middlewares/AuthMiddleware.php";
  }

  /**
   * @throws \Exception
   * @throws \Pecee\Http\Middleware\Exceptions\TokenMismatchException
   * @throws \Pecee\SimpleRouter\Exceptions\HttpException
   * @throws \Pecee\SimpleRouter\Exceptions\NotFoundHttpException
   */
  public static function loadHandlerException(string $root): void
  {
    require_once $root . "/app/Handlers/CustomExceptionHandler.php";
    require_once $root . "/app/Handlers/SalesExceptionHandler.php";
    require_once $root . "/app/Handlers/UsersExceptionHandler.php";
  }

  /**
   * @throws \Exception
   * @throws \Pecee\Http\Middleware\Exceptions\TokenMismatchException
   * @throws \Pecee\SimpleRouter\Exceptions\HttpException
   * @throws \Pecee\SimpleRouter\Exceptions\NotFoundHttpException
   */
  public static function loadControllers(string $root): void
  {
    require_once $root . "/app/Controllers/UtilsController.php";
    require_once $root . "/app/Controllers/ConnectionController.php";
    require_once $root . "/app/Controllers/DefaultController.php";
    require_once $root . "/app/Controllers/ServicesController.php";
    require_once $root . "/app/Controllers/ShipsController.php";
    require_once $root . "/app/Controllers/SalesController.php";
    require_once $root . "/app/Controllers/UsersController.php";
  }

  /**
   * @throws \Exception
   * @throws \Pecee\Http\Middleware\Exceptions\TokenMismatchException
   * @throws \Pecee\SimpleRouter\Exceptions\HttpException
   * @throws \Pecee\SimpleRouter\Exceptions\NotFoundHttpException
   */
  public static function loadCustomRoutes(string $root): void
  {
    require_once $root . "/src/routes/web.php";
  }
}
