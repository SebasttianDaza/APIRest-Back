<?php

namespace Ships;

use Pecee\SimpleRouter\SimpleRouter;
require_once __DIR__ . "/App/Handlers/CustomExceptionHandler.php";
require_once __DIR__ . "/App/Middlewares/AuthMiddleware.php";
require_once __DIR__ . "/App/Controllers/UtilsController.php";
require_once __DIR__ . "/App/Controllers/ConnectionController.php";
require_once __DIR__ . "/App/Controllers/DefaultController.php";
require_once __DIR__ . "/App/Controllers/ServicesController.php";
require_once __DIR__ . "/App/Controllers/ShipsController.php";
require_once __DIR__ . "/App/Controllers/SalesController.php";
require_once __DIR__ . "/App/Controllers/UsersController.php";

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
    // Load our helpers
    require_once __DIR__ . "/helpers.php";

    parent::setDefaultNamespace("Ships\Controllers");

    // Load our custom routes
    require_once __DIR__ . "/routes/web.php";

    // Do initial stuff
    parent::start();
  }
}

?>
