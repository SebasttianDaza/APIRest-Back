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

    $query = "SELECT * FROM Embarcaciones LIMIT $start, $count";
    $result = parent::getData($query);

    response()->httpCode(200);
    return response()->json([
      "Page" => $page,
			"NextPage" => $page + 1,
			"PrevPage" => $page - 1,
      "StatusMsg" => "OK",
      "StatusCode" => 200,
      "Ships" => $result,
      "detail" => "It's a list of ships",
      "instance" => $url,
    ]);
  }

  /**
   * Get a ship by id
   * @param int $id
   * @author SebastianDaza
   * @return json
   */
  private function getEmbarcacion(int $id, string $url = null)
  {
    $query = "SELECT * FROM Embarcaciones WHERE id = $id";
    $result = parent::getData($query);

    response()->httpCode(200);
    return response()->json([
      "StatusMsg" => "OK",
      "StatusCode" => 200,
      "Ship" => $result,
      "detail" => "It's a ship",
      "instance" => $url,
    ]);
  }

  /**
   * Create a ship with all information
   * @param array $data
   * @return json
   */
  public function createEmbarcacion(array $data, string $url = null)
  {

    if (!isset($data["token"])) {
      response()->httpCode(401);
      // User not has token
      return response()->json([
        "StatusMsg" => "Unauthorized or unauthenticated",
        "StatusCode" => 401,
        "detail" => "You need a token to create a ship",
        "instance" => $url,
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
						"detail" => "You need to send all information",
						"instance" => $url,
          ]);
        } else {
          $this->insertEmbarcacion($data);

          if ($result) {
            response()->httpCode(201);
            return response()->json([
              "StatusMsg" => "OK",
              "StatusCode" => 201,
							"detail" => "Ship created",
              "data" => $result,
							"instance" => $url,
            ]);
          }

          if (!$result) {
            response()->httpCode(500);
            return response()->json([
              "StatusMsg" => "Internal server error",
              "StatusCode" => 500,
							"detail" => "Ship not created",
							"instance" => $url,
            ]);
          }
        }
      }

      if (!$dataToken) {
        response()->httpCode(401);
        return response()->json([
          "StatusMsg" => "Unauthorized or unauthenticated",
          "StatusCode" => 401,
					"detail" => "You need a token to create a ship",
					"instance" => $url,
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
   * @param array $data
	 * @param string $url
   * @return json
   */
	public function updateShip(array $data, string $url = null)
  {
    if (!isset($data["id"])) {
      // Bad request
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
          "StatusMsg" => "OK",
          "StatusCode" => 200,
          "id" => $this->id,
          "Ship" => $result,
					"detail" => "Ship updated",
					"instance" => $url,
        ]);
      }

      if (!$result) {
        response()->httpCode(500);
        return response()->json([
          "StatusMsg" => "Internal server error",
          "StatusCode" => 500,
					"detail" => "Ship not updated",
					"instance" => $url,
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

    if ($response >= 1) return $response;

    if ($response < 1) return false;
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

    if ($response >= 1) return $response;
    if ($response < 1) return false;
  }

  /**
   *
   */
  private function searchToken()
  {
    $query = "SELECT tokenID, userID, token, status FROM users_token WHERE token = '$this->token' AND status = 'active'";
    $response = parent::getData($query);

    if ($response) return $response;

    if (!$response)	return false;
  }

  /**
   *
   */
  private function updateToken($tokenId)
  {
    $date = date("Y-m-d H:i:s");
    $query = "UPDATE users_token SET date = '$date' WHERE tokenID = $tokenId";

    $response = parent::anyQuery($query);

    if ($response >= 1) return $response;

    if ($response < 1)	return false;
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
    $result = $this->getEmbarcacion(
			$id,
			url("ships", "ShipsController@getShipAction")
		);
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
    $object = input()->all();
		$url = url("ships", "ShipsController@postShipsAction");

    if (!empty($object)) {
      $result = $this->createEmbarcacion($object, $url);
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
