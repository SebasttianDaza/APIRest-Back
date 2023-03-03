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
      $this->httpException(
        $headers ?? "Unauthorized or unauthenticated",
        401,
        $request->getUrl()
      );
      return;
    }

    $tokendb = $this->connectionController->getToken($headers);

    // Check if token is valid
    if (!$tokendb) {
      $this->httpException(
        "Unauthorized or unauthenticated",
        401,
        $request->getUrl()
      );
      return;
    }

    $responseToken = $this->connectionController->getUpdateToken(
      $this->utilsController->array_value_recursive("userID", current($tokendb))
    );

    if (!$responseToken) {
      $this->httpException(
        "Internal server error",
        500,
        $request->getUrl()
      );
      return;
    }
  }

  /**
   * @param string $message
   * @param int $code
   * @param string $instance
   * @return array
   */
  public function httpException(
    string $message,
    int $code,
    string $instance
  ): array {
    response()->httpCode($code);
    return response()->json([
      "StatusMsg" => preg_replace("/\r|\n/", "", $message),
      "StatusCode" => $code,
      "instance" => preg_replace("/\r|\n/", "", $instance),
    ]);
  }
}

?>
