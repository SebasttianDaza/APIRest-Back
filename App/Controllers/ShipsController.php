<?php

namespace Ships\Controllers;

class ShipsController extends ConnectionController
{
  private $id;
  private $name = "";
  private $country = "";
  private $continent = "";
  private $coordinates = "";
  private $token = "";

  /**
   * Get ships per page
   * @param int $page
   * @author SebastianDaza
   * @return json
   */
  private function getListShips(int $page = 1, string $url = null)
  {
    $start = 0;
    $count = 100;

    if ($page > 1) {
      // Get the start position and the end position
      $start = $count * ($page - 1) + 1;
      $count = $count * $page;
    }

    $response = parent::getItems("Embarcaciones", $start, $count);

    if (!$response) {
      response()->httpCode(404);
      return response()->json([
        "StatusMsg" => "Not Found",
        "StatusCode" => 404,
        "detail" => "No ships found",
        "instance" => $url ?? null,
      ]);
    }

    response()->httpCode(200);
    return response()->json([
      "Page" => $page,
      "NextPage" => $page + 1,
      "PrevPage" => $page - 1,
      "StatusMsg" => "OK",
      "StatusCode" => 200,
      "Ships" => $response,
      "detail" => "It's a list of ships",
      "instance" => $url ?? null,
    ]);
  }

  /**
   * Get a ship by id
   * @param int $id
   * @author SebastianDaza
   * @return json
   */
  private function getShip(int $id, string $url = null)
  {
    $response = parent::getItemById("Embarcaciones", $id);

    if (!$response) {
      response()->httpCode(404);
      return response()->json([
        "StatusMsg" => "Not Found",
        "StatusCode" => 404,
        "detail" => "No ship found",
        "instance" => $url ?? null,
      ]);
    }

    response()->httpCode(200);
    return response()->json([
      "StatusMsg" => "OK",
      "StatusCode" => 200,
      "Ship" => $response,
      "detail" => "It's a ship",
      "instance" => $url ?? null,
    ]);
  }

  /**
   * Create a ship with all information
   * @param array $data
   * @return json
   */
  private function createShip(array $data, string $url = null)
  {
    // User not has token
    if (!isset($data["token"])) {
      response()->httpCode(401);
      return response()->json([
        "StatusMsg" => "Unauthorized or unauthenticated",
        "StatusCode" => 401,
        "detail" => "You need a token to create a ship",
        "instance" => $url,
      ]);
    }

    if ($data && array_key_exists("token", $data) && isset($data["token"])) {
      $this->token = $data["token"];
      $tokendb = parent::getToken($this->token);
      $tokenId;

      // No token found
      if (!$tokendb) {
        response()->httpCode(401);
        return response()->json([
          "StatusMsg" => "Unauthorized or unauthenticated",
          "StatusCode" => 401,
          "detail" => "You need a token to create a ship",
          "instance" => $url,
        ]);
      }

      // Update token
      $responseToken = parent::getUpdateToken(
        parent::array_value_recursive("userID", current($tokendb))
      );

      if (!$responseToken) {
        response()->httpCode(500);
        return response()->json([
          "StatusMsg" => "Internal server error",
          "StatusCode" => 500,
          "detail" => "Token not updated",
          "instance" => $url,
        ]);
      }

      $keysnecessary = ["name", "country", "continent", "coordinates"];

      if (parent::getInArray($data, $keysnecessary)) {
        response()->httpCode(400);
        return response()->json([
          "StatusMsg" => "Bad request",
          "StatusCode" => 400,
          "detail" => "You need to send all information",
          "instance" => $url,
        ]);
      }
      // Remove token
      unset($data["token"]);

      // Insert ship
      $result = parent::setItem("Embarcaciones", $data);

      // Error in insert
      if (!$result) {
        response()->httpCode(500);
        return response()->json([
          "StatusMsg" => "Internal server error",
          "StatusCode" => 500,
          "detail" => "Ship not created",
          "instance" => $url,
        ]);
      }

      response()->httpCode(201);
      return response()->json([
        "StatusMsg" => "OK",
        "StatusCode" => 201,
        "detail" => "Ship created",
        "data" => $result,
        "instance" => $url,
      ]);
    }
  }


  /**
   * @param array $data
   * @param string $url
   * @return json
   */
  public function updateShip(array $data, string $url = null)
  {
    $keys = ["id", "name", "country", "continent", "coordinates"];

    if (!$data || !$this->getInArray($keys, $data) || !isset($data["id"])) {
      // Bad request
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "You need to send the id of the ship",
        "instance" => $url,
      ]);
    }

    $this->id = $data["id"];

    if (isset($data["name"])) {
      $this->name = $data["name"];
    }

    if (isset($data["country"])) {
      $this->country = $data["country"];
    }

    if (isset($data["continent"])) {
      $this->continent = $data["continent"];
    }

    if (isset($data["coordinates"])) {
      $this->coordinates = $data["coordinates"];
    }

    $result = $this->updateShipData();

    if (!$result) {
      response()->httpCode(500);
      return response()->json([
        "StatusMsg" => "Internal server error",
        "StatusCode" => 500,
        "detail" => "Ship not updated",
        "instance" => $url,
      ]);
    }

    response()->httpCode(200);
    return response()->json([
      "StatusMsg" => "OK",
      "StatusCode" => 200,
      "id" => $this->id,
      "Ship" => $result,
      "detail" => "Ship updated",
      "instance" => $url,
    ]);
  }

  /**
   *
   */
  private function updateShipData()
  {
    //Si el valor es un string vacio, no se actualiza
    $query = "UPDATE Embarcaciones SET ";

    if ($this->name != "") {
      $query .= "name = '$this->name', ";
    }
    if ($this->country != "") {
      $query .= "country = '$this->country', ";
    }
    if ($this->continent != "") {
      $query .= "continent = '$this->continent', ";
    }
    if ($this->coordinates != "") {
      $query .= "coordinates = '$this->coordinates' ";
    }

    //Si solo es uno, quita la ultima coma
    if (strlen($query) > strlen("UPDATE Embarcaciones SET ")) {
      $query = substr($query, 0, strlen($query) - 2);
    }

    $query .= " WHERE id = $this->id";

    $response = parent::anyQuery($query);

    if ($response >= 1) {
      return $response;
    }

    if ($response < 1) {
      return false;
    }
  }

  /**
   *	@param array $data
   *	@param string $url
   *	@return json
   */
  public function deleteShip(array $data, string $url = null)
  {
    if (!isset($data["id"])) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "You need to send the id of the ship",
        "instance" => $url,
      ]);
    }

    if (isset($data["id"])) {
      $this->id = $data["id"];

      $result = $this->delete();

      if ($result) {
        response()->httpCode(200);
        return response()->json([
          "StatusMsg" => "OK",
          "StatusCode" => 200,
          "id" => $this->id,
          "count" => $result,
          "detail" => "Ship deleted",
          "instance" => $url,
        ]);
      }

      if (!$result) {
        response()->httpCode(500);
        return response()->json([
          "StatusMsg" => "Internal server error",
          "StatusCode" => 500,
          "detail" => "Ship not deleted",
          "instance" => $url,
        ]);
      }
    }
  }

  /**
   *
   */
  private function delete()
  {
    $query = "DELETE FROM Embarcaciones WHERE id = $this->id";

    $response = parent::anyQuery($query);

    if ($response >= 1) {
      return $response;
    }
    if ($response < 1) {
      return false;
    }
  }

  /**
   * @Route("/ships/{page?}")
   * @Method({"GET"})
   * @param int $page
   * @return json
   * @throws Exception
   */
  public function getShipsAction($page = null): string
  {
    $result = $this->getListShips(
      $page,
      url("ships", "ShipsController@getShipsAction")
    );
    return $result;
  }

  /**
   *	@Route("/ships/{id}")
   *	@Method({"GET"})
   *	@param int $id
   *	@return json
   *	@throws Exception
   */
  public function getShipAction($id): string
  {
    $result = $this->getShip($id, url("ship", "ShipsController@getShipAction"));
    return $result;
  }

  /**
   *	@Route("/ships")
   *	@Method({"POST"})
   *	@return json
   *	@throws Exception
   */
  public function postShipsAction()
  {
    $post = input()->all();
    $url = url("ships", "ShipsController@postShipsAction");

    if (empty($post)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Do not send empty data",
        "instance" => $url,
      ]);
    }

    return $this->createShip($post, $url);
  }

  /**
   * 	@Route("/ships")
   * 	@Method({"PUT"})
   * 	@return json
   */
  public function putShipsAction()
  {
    $object = input()->all();
    $url = url("ships", "ShipsController@putShipsAction");

    if (!empty($object)) {
      $result = $this->updateShip($object, $url);
      return $result;
    } else {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Do not send empty data",
        "instance" => $url,
      ]);
    }
  }

  /**
   *	@Route("/ships")
   *	@Method({"DELETE"})
   *	@return json
   */
  public function deleteShipsAction()
  {
    $object = input()->all();
    $url = url("ships", "ShipsController@deleteShipsAction");

    if (!empty($object)) {
      $result = $this->deleteShip($object, $url);
      return $result;
    } else {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Do not send empty data",
        "instance" => $url,
      ]);
    }
  }
}
