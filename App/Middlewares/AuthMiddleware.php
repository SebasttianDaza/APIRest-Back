<?php

namespace Ships\Middlewares;

use Ships\Controllers\UtilsController;
use Ships\Controllers\ConnectionController;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Pecee\SimpleRouter\Exceptions\HttpException;

class AuthMiddleware implements IMiddleware
{
  /**
   * @var UtilsController
   */
  private $utilsController;

  /**
   * @var ConnectionController
   */
  private $connectionController;

  /**
   * @Constructor
   * @Return void
   * @throws \Exception
   * @throws \Error
   */
  public function __construct()
  {
    $this->utilsController = new UtilsController();
    $this->connectionController = new ConnectionController();
  }

  public function handle(Request $request): void
  {
    //Look function in request
    $headers = $request->getHeader(
      "X-Auth-Token",
      "There is no Authorization header",
      true
    );

    if (
      strcmp($headers, "There is no Authorization header") === 0 ||
      empty($headers)
    ) {
      // Return throw exception
      throw new HttpException($headers ? $headers : "No empty header", 401);
    }

    $tokendb = $this->connectionController->getToken($headers);

    // Check if token is valid
    if (!$tokendb) {
      throw new HttpException("Unauthorized or unauthenticated", 401);
    }

    $responseToken = $this->connectionController->getUpdateToken(
      $this->utilsController->array_value_recursive("userID", current($tokendb))
    );

    if (!$responseToken) {
      throw new HttpException("Internal server error", 500);
    }
  }
}

?>
