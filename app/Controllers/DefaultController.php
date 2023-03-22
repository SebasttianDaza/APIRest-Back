<?php

namespace Ships\Controllers;
use Pecee\Http\Request;
use Ships\Controllers\UtilsController;

class DefaultController
{
  /**
   * @var UtilsController
   */
  private $utilsController;

  public function __construct()
  {
    $this->utilsController = new UtilsController();
  }

  /**
   * @Constructor
   * @Return void
   * @throws \Exception
   * @throws \Error
   */
  public function home()
  {
    return redirect($_ENV["URL_HOME"], 301);
  }

  public function contact(): string
  {
    return "DefaultController -> contact";
  }

  /**
   * @desc: Default 404 error
   * @return array
   * @throws \Pecee\Http\Exceptions\HttpException
   * @throws \Pecee\Http\Exceptions\HttpResponseException
   * @throws \Pecee\SimpleRouter\Exceptions\NotFoundHttpException
   */
  public function notFound(): array
  {
    response()->httpCode(404);
    return response()->json([
      "StatusMsg" => "Not Found",
      "StatusCode" => 404,
      "detail" => "The requested resource was not found",
    ]);
  }
}

