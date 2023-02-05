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
  public function getListShips(int $page = 1)
  {
    $start = 0;
    $count = 100;

    if ($page > 1) {
      // Get the start position
      $start = $count * ($page - 1) + 1;
      // Get the end position
      $count = $count * $page;
    }

    $query = "SELECT * FROM Embarcaciones LIMIT $start, $count";
    $result = parent::getData($query);

    response()->httpCode(200);
    return response()->json($result);
  }

  /**
   * Get a ship by id
   * @param int $id
   * @author SebastianDaza
   * @return json
   */
  public function getEmbarcacion(int $id)
  {
    $query = "SELECT * FROM Embarcaciones WHERE id = $id";
    $result = parent::getData($query);

    response()->httpCode(200);
    return response()->json($result);
  }

  /**
   * Create a ship with all information
   * @param array $data
   * @return json
   */
  public function createEmbarcacion(array $data)
  {
    if (!isset($data["token"])) {
      response()->httpCode(401);
      // User not has token
      return response()->json([
        "error" => "Unauthorized",
        "code" => 401,
      ]);
    }

    if (isset($data["token"])) {
      $this->token = $data["token"];
      $dataToken = $this->searchToken();
      $tokenId;

      if ($dataToken) {
        foreach ($dataToken as $key => $value) {
          $tokenId = $value["tokenID"];
        }

        $this->updateToken($tokenId);

        if (
          !isset($data["name"]) ||
          !isset($data["country"]) ||
          !isset($data["continent"]) ||
          !isset($data["coordinates"])
        ) {
          response()->httpCode(400);
          return response()->json([
            "StatusMsg" => "Bad request",
            "StatusCode" => 400,
          ]);
        } else {
          $result = $this->insertEmbarcacion($data);

          if ($result) {
            response()->httpCode(201);
            return response()->json([
              "StatusMsg" => "success",
              "data" => $result,
            ]);
          }

          if (!$result) {
            response()->httpCode(500);
            return response()->json([
              "StatusMsg" => "Internal server error",
              "StatusCode" => 500,
            ]);
          }
        }
      }

      if (!$dataToken) {
        response()->httpCode(401);
        return response()->json([
          "StatusMsg" => "Unauthorized",
          "StatusCode" => 401,
        ]);
      }
    }
  }

  /**
   *  @param array $data
   *  @return json
   */
  private function insertEmbarcacion(array $data)
  {
    $name = $data["name"];
    $country = $data["country"];
    $continent = $data["continent"];
    $coordinates = $data["coordinates"];

    $query = "INSERT INTO Embarcaciones (id, name, country, continent, coordinates) VALUES (NULL,'$name', '$country', '$continent', '$coordinates')";

    return parent::anyQueryID($query);
  }

  /**
   *
   */
  public function updateShip($data)
  {
    if (!isset($data["id"])) {
      return response()->json([
        "error" => "Bad request",
        "code" => 400,
      ]);
    }

    if (isset($data["id"])) {
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

      if ($result) {
        response()->httpCode(200);
        return response()->json([
          "status" => "success",
          "id" => $this->id,
          "data" => $result,
          "code" => 200,
        ]);
      }

      if (!$result) {
        response()->httpCode(500);
        return response()->json([
          "error" => "Internal server error",
          "code" => 500,
        ]);
      }
    }
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
   *
   */
  public function deleteShip($data)
  {
    if (!isset($data["id"])) {
      return response()->json([
        "error" => "Bad request",
        "code" => 400,
      ]);
    }

    if (isset($data["id"])) {
      $this->id = $data["id"];

      $result = $this->delete();

      if ($result) {
        return response()->json([
          "status" => "success",
          "id" => $this->id,
          "count" => $result,
          "code" => 200,
        ]);
      }

      if (!$result) {
        return response()->json([
          "error" => "Internal server error",
          "code" => 500,
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
   *
   */
  private function searchToken()
  {
    $query = "SELECT tokenID, userID, token, status FROM users_token WHERE token = '$this->token' AND status = 'active'";
    $response = parent::getData($query);

    if ($response) {
      return $response;
    }

    if (!$response) {
      return false;
    }
  }

  /**
   *
   */
  private function updateToken($tokenId)
  {
    $date = date("Y-m-d H:i:s");
    $query = "UPDATE users_token SET date = '$date' WHERE tokenID = $tokenId";

    $response = parent::anyQuery($query);

    if ($response >= 1) {
      return $response;
    }

    if ($response < 1) {
      return false;
    }
  }

  /**
   *
   */
  public function getShipsAction($page = null): string
  {
    $result = $this->getListShips($page);
    return $result;
  }

  /**
   *
   */
  public function getShipAction($id): string
  {
    $result = $this->getEmbarcacion($id);
    return $result;
  }

  /**
   *
   */
  public function postShipsAction()
  {
    $object = input()->all();

    if (!empty($object)) {
      $result = $this->createEmbarcacion($object);
      return $result;
    } else {
      return response()->json([
        "status" => "error",
        "code" => 400,
        "message" => "No se ha recibido ningun dato",
      ]);
    }
  }

  /**
   *
   */
  public function putShipsAction()
  {
    $object = input()->all();

    if (!empty($object)) {
      $result = $this->updateShip($object);
      return $result;
    } else {
      return response()->json([
        "status" => "error",
        "code" => 400,
        "message" => "No se ha recibido ningun dato",
      ]);
    }
  }

  /**
   *
   */

  public function deleteShipsAction()
  {
    $object = input()->all();

    if (!empty($object)) {
      $result = $this->deleteShip($object);
      return $result;
    } else {
      return response()->json([
        "status" => "error",
        "code" => 400,
        "message" => "No se ha recibido ningun dato",
      ]);
    }
  }
}
