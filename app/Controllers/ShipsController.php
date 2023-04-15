<?php

namespace Ships\Controllers;

class ShipsController extends ConnectionController
{
  /**
   * @var int $id
   */
  private $id;

  /**
   * @var string $name
   */
  private $name = "";

  /**
   * @var string $country
   */
  private $country = "";

  /**
   * @var string $continent
   */
  private $continent = "";

  /**
   * @var string $coordinates
   */
  private $coordinates = "";

  /**
   * @var string $token
   */
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

    $response = parent::getItems("embarcaciones", $start, $count);

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
    $response = parent::getItemById("embarcaciones", $id);

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

    // Insert ship
    $result = parent::setItem("embarcaciones", $data);

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

  /**
   * @param array $data
   * @param string $url
   * @return json
   */
  private function updateShip(array $data, string $url = null): array
  {
    // User not has id
    if (
      !array_key_exists("id", $data) ||
      empty($data["id"]) ||
      !is_numeric($data["id"]) ||
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

    $this->id = $data["id"];
    // Remove id
    unset($data["id"]);

    $response = parent::updateItem("embarcaciones", $data, $this->id);

    if (!$response) {
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
      "Ship" => $response,
      "detail" => "Ship updated",
      "instance" => $url,
    ]);
  }

  /**
   *	@param array $data
   *	@param string $url
   *	@return json
   */
  private function deleteShip(int $id, string $url = null)
  {
    $response = parent::removeItem("embarcaciones", $id);

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
      "detail" => "Ship deleted",
      "instance" => $url ?? null,
    ]);
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
    $put = input()->all();
    $url = url("ships", "ShipsController@putShipsAction");

    if (empty($put)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Do not send empty data",
        "instance" => $url,
      ]);
    }

    return $this->updateShip($put, $url);
  }

  /**
   *	@Route("/ships")
   *	@Method({"DELETE"})
   *	@return json
   */
  public function deleteShipsAction(int $id = null): array
  {
    $url = url("ships", "ShipsController@deleteShipsAction");

    if (empty($id)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Do not send empty data",
        "instance" => $url,
      ]);
    }

    return $this->deleteShip($id, $url);
  }
}
