<?php
namespace Ships\Controllers;

use Ships\Controllers;

class UsersController extends ConnectionController
{
  /**
   * @var int $userID
   */
  private $userID;

  /**
   * @var string $username
   */
  private $username = "";

  /**
   * @var string $lastname
   */
  private $lastname = "";

  /**
   * @var string $email
   */
  private $email = "";

  /**
   * @var int $embarcacionesID
   */
  private $embarcacionesID = "";


  /**
   * Get users per page
   * @param int $page
   * @return array
   */
  private function getListUsers(int $page = 1, string $url = null): array
  {
    $start = 0;
    $count = 100;

    if ($page > 1) {
      $start = $count * ($page - 1) + 1;
      $count = $count * $page;
    }

    $response = parent::getItems("usuarios", $start, $count);

    if (!$response) {
      response()->httpCode(404);
      return response()->json([
        "StatusMsg" => "Not found",
        "StatusCode" => 404,
        "detail" => "No sales found",
        "instance" => $url ?? null
      ]);
    }

    response()->httpCode(200);
    return response()->json([
      "Page" => $page,
      "NextPage" => $page + 1,
      "PrevPage" => $page - 1,
      "StatusMsg" => "OK",
      "StatusCode" => 200,
      "Users" => $response,
      "detail" => "It's a list of users",
      "instance" => $url ?? null
    ]);

  }

  /**
   * Get user by id
   * @param int $id
   * @param string $url
   * @author SebastianDaza
   * @return json
   */
  private function getUser(int $id = 0, string $url = null): array
  {
    $response = parent::getItemById("usuarios", $id, "userID");

    if (!$response) {
      response()->httpCode(404);
      return response()->json([
        "StatusMsg" => "Not found",
        "StatusCode" => 404,
        "detail" => "No user found",
        "instance" => $url ?? null
      ]);
    }

    response()->httpCode(200);
    return response()->json([
      "StatusMsg" => "OK",
      "StatusCode" => 200,
      "User" => $response,
      "detail" => "It's a user",
      "instance" => $url ?? null
    ]);
  }

  /**
   * Create a user with all information
   * @param array $data
   * @return array
   */
  private function createUser(array $data, string $url = null): array
  {
    $keysneccesary = ["username", "lastname", "email", "embarcacionesID"];

    if (parent::getInArray($data, $keysneccesary)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "You need to send all information",
        "instance" => $url
      ]);
    }

    $response = parent::setItem("usuarios", $data);

    if (!$response) {
      response()->httpCode(500);
      return response()->json([
        "StatusMsg" => "Internal server error",
        "StatusCode" => 500,
        "detail" => "User not created",
        "instance" => $url
      ]);
    }

    response()->httpCode(201);
    return response()->json([
      "StatusMsg" => "OK",
      "StatusCode" => 201,
      "detail" => "Sale created",
      "data" => $response,
      "instance" => $url
    ]);
  }


  /**
   * @param array $data
   * @param string $url
   * @return array
   */
  private function updateUser(array $data, string $url = null): array
  {
    if (
      !array_key_exists("userID", $data) ||
      empty($data["userID"]) ||
      !is_numeric($data["userID"]) ||
      count($data) <= 1
    ) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "You need to send all information",
        "instance" => $url,
      ]);
    }

    $this->userID = $data["userID"];
    unset($data["userID"]);

    $response = parent::updateItem("usuarios", $data, $this->userID, "userID");

    if (!$response) {
      response()->httpCode(500);
      return response()->json([
        "StatusMsg" => "Internal server error",
        "StatusCode" => 500,
        "detail" => "User not updated",
        "instance" => $url,
      ]);
    }

    response()->httpCode(200);
    return response()->json([
      "StatusMsg" => "OK",
      "StatusCode" => 200,
      "id" => $this->userID,
      "Sale" => $response,
      "detail" => "User updated",
      "instance" => $url,
    ]);
  }

  /**
   * @param int $id
   * @param string $url
   * @return array
   */
  private function deleteUser(int $id, string $url = null): array
  {
    $response = parent::removeItem("usuarios", $id, "userID");

    if (!$response) {
      response()->httpCode(404);
      return response()->json([
        "StatusMsg" => "Not Found",
        "StatusCode" => 404,
        "detail" => "No user found",
        "instance" => $url ?? null,
      ]);
    }

    response()->httpCode(200);
    return response()->json([
      "StatusMsg" => "OK",
      "StatusCode" => 200,
      "Sale" => $response,
      "detail" => "User deleted",
      "instance" => $url ?? null,
    ]);
  }

  /**
   * @Route("/users/{page}")
   * #Method({"GET"})
   * @param int $page
   * @return array
   * @throws Exception
   * @author SebastianDaza
   */
  public function getUsersAction($page = null): array
  {
    return self::getListUsers(
      $page,
      url("users", "UserController@getUsersAction")
    );
  }

  /**
   * @Route("user/{id}")
   * @Method({"GET"})
   * @param int $id
   * @throws Exception
   * @return array
   */
  public function getUserAction(int $id = null): array
  {
    return self::getUser(
      $id,
      url("user", "UserController@getUserAction")
    );
  }

  /**
   * @Route("/users")
   * @Method({"POST"})
   * @return json
   * @throws Exception
   */
  public function postUsersAction(): array
  {
    $post = input()->all();
    $url = url("users", "UsersController@postUsersAction");

    if (empty($post)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Do not send empty data",
        "instance" => $url,
      ]);
    }

    return self::createUser($post, $url);
  }

  /**
   * @Route("/users")
   * @Method({"PUT"})
   * @return array
   */
  public function putUsersAction(): array
  {
    $put = input()->all();
    $url = url("users", "UsersController@putUsersAction");

    if (empty($put)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Do not send empty data",
        "instance" => $url,
      ]);
    }

    return self::updateUser($put, $url);
  }

  /**
   * @Route("/user/{$id}")
   * @Method({"DELETE"})
   * @return array
   */
  public function deleteUserAction(int $id = null): array
  {
    $url = url("user", "UserController@deleteUserAction");

    if (empty($id)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Do not send empty data",
        "instance" => $url,
      ]);
    }
    
    return self::deleteUser($id, $url);
  }
}
