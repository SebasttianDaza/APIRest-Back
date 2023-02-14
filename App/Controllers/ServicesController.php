<?php

namespace Ships\Controllers;

use Pecee\Http\Input\InputHandler;
use Ships\Controllers;

class ServicesController extends ConnectionController
{
  public $data = [];

  /**
   * @param array $data
   */
  private function getLogin(array $data, string $instance): array
  {
    //Check if data is not empty and username and password is set
    if (
      !$data ||
      !array_key_exists("username", $data) ||
      !array_key_exists("password", $data) ||
      empty($data["username"]) ||
      empty($data["password"])
    ) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Username or password is not set",
        "instance" => $instance,
      ]);
    }
    // Destructure data ["username" => $username, "password" => $password]
    extract($data, EXTR_PREFIX_SAME, "wddx");
    $username;
    $password;

    //Encrypt password
    $password = parent::encrypt($password);

    $table = parent::getDataUsers($username, "userID");

    // Not user found
    if (!$table) {
      response()->httpCode(404);
      return response()->json([
        "StatusMsg" => "Not found",
        "StatusCode" => 404,
        "detail" => "User not found",
        "instance" => $instance,
      ]);
    }

    // Get the first item from array
    $user = current($table);

    if (array_key_exists("status", $user)) {
      // Check if user is active
      if ($user["status"] == 0) {
        response()->httpCode(401);
        return response()->json([
          "StatusMsg" => "Unauthorized",
          "StatusCode" => 401,
          "detail" => "User is not active",
          "instance" => $instance,
        ]);
      }

      if (
        array_key_exists("password", $user) &&
        strcmp(parent::encrypt($user["password"]), $password) == 0
      ) {
        // Create token if password is correct
        $token = $this->createToken($user["userID"]);

        if (!$token) {
          response()->httpCode(500);
          return response()->json([
            "StatusMsg" => "Internal server error",
            "StatusCode" => 500,
            "detail" => "Internal server error",
            "instance" => $instance,
          ]);
        }

        response()->httpCode(200);
        return response()->json([
          "StatusMsg" => "Success",
          "StatusCode" => 200,
          "detail" => "User logged in",
          "instance" => $instance,
          "token" => $token,
        ]);
      }
    }
  }

  /**
   *  Set user in database
   * @param array $response
   * @param string $instance
   * @return array
   */
  private function setRegister(array $response, string $instance): array
  {
    $this->data = $response;
    // Destructuring data
    extract($this->data, EXTR_PREFIX_SAME, "wddx");
    $username;
    $email;
    $password;

    $response = parent::setUser($username, $email, $password);

    if ($response <= 0) {
      response()->httpCode(409);
      return response()->json([
        "StatusMsg" => "Conflict",
        "StatusCode" => 409,
        "detail" => "User already exists",
        "instance" => $instance,
      ]);
    }

    // User created
    response()->httpCode(200);
    return response()->json([
      "StatusMsg" => "Success",
      "StatusCode" => 200,
      "detail" => "User created successfully",
      "instance" => $instance,
    ]);
  }

  /**
   * Create token for user using
   * @param int $userID
   * @return string|bool
   */
  private function createToken(int $userID): string|bool
  {
    $val = true;
    $token = bin2hex(openssl_random_pseudo_bytes(16, $val));

    $response = parent::setToken($userID, $token, 1);
    return $response ? $token : false;
  }

  /**
   * Register user in database
   * @Route("/register")
   * @return json
   * @param json
   */
  public function registerAction(): string
  {
    $response = input()->all();
    $url = url("register", "ServicesController@registerAction");

    if (
      empty($response) ||
      parent::getInArray($response, ["username", "email", "password"])
    ) {
      response()->httpCode(500);
      return response()->json([
        "StatusMsg" => "Internal Server Error",
        "StatusCode" => 500,
        "detail" => "No data",
        "instance" => $url,
      ]);
    }

    return $this->setRegister($response, $url);
  }

  /**
   * @Route("/auth)
   * @method("POST")
   * @return json
   * @description Authentication user and return token
   */
  public function authAction(): array
  {
    $post = input()->all();
    $url = url("auth", "ServicesController@authAction");

    if (empty($post)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad Request",
        "StatusCode" => 400,
        "detail" => "No data",
        "instance" => $url,
      ]);
    }

    return $this->getLogin($post, $url);
  }
}

?>
