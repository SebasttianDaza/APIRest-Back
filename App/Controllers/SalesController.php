<?php
namespace Ships\Controllers;

use Ships\Controllers;

class SalesController extends ConnectionController
{
  /**
   * @var int $saleID
   */
  private $saleID;

  /**
   * @var int $embarcacionesID
   */
  private $embarcacionesID = "";

  /**
   * @var int $quantity
   */
  private $quantity = "";

  /**
   * Get sales per page
   * @param int $page
   * @author SebastianDaza
   * @return json
   */
  private function getListSales(int $page = 1, string $url = null): array
  {
    $start = 0;
    $count = 100;

    if ($page > 1) {
      $start = $count * ($page - 1) + 1;
      $count = $count * $page;
    }

    $response = parent::getItems("sale", $start, $count);

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
      "Sales" => $response,
      "detail" => "It's a list of sales",
      "instance" => $url ?? null
    ]);
  }

  /**
   * Get sale by id
   * @param int $id
   * @param string $url
   * @author SebastianDaza
   * @return json
   */
  private function getSale(int $id = 0, string $url = null)
  {
    $response = parent::getItemById("sale", $id, "saleID");
    
    if (!$response) {
      response()->httpCode(404);
      return response()->json([
        "StatusMsg" => "Not found",
        "StatusCode" => 404,
        "detail" => "No sale found",
        "instance" => $url ?? null
      ]);
    }

    response()->httpCode(200);
    return response()->json([
      "StatusMsg" => "OK",
      "StatusCode" => 200,
      "Sale" => $response,
      "detail" => "It's a sale",
      "instance" => $url ?? null
    ]);
  }

  /**
   * Create a ship with all information
   * @param array $data
   * @return json
   */
  private function createSale(array $data, string $url = null): array
  {
    $keysnecessary = ["embarcacionesID", "quantity"];

    if (parent::getInArray($data, $keysnecessary)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "You need to send all information",
        "instance" => $url
      ]);
    }

    $response = parent::setItem("sale", $data);

    if (!$response) {
      response()->httpCode(500);
      return response()->json([
        "StatusMsg" => "Internal server error",
        "StatusCode" => 500,
        "detail" => "Sale not created",
        "instance" => $url
      ]);
    }

    response()->httpCode(201);
    return response()->json([
      "StatusMsg" => "OK",
      "StatusCode" => 201,
      "detail" => "Sale created",
      "data" => $resutl,
      "instance" => $url
    ]);
  }

  /**
   *
   */
  public function insertSale($data)
  {
    $this->embarcacionesID = $data["embarcacionesID"];
    $this->quantity = $data["quantity"];

    $query = "INSERT INTO sale (embarcacionesID, quantity) VALUES ('$this->embarcacionesID', '$this->quantity')";

    $result = parent::anyQueryID($query);

    if ($result) {
      return $result;
    }

    if (!$result) {
      return false;
    }
  }

  /**
   *
   */
  public function put($data)
  {
    if (!isset($data["saleID"])) {
      return response()->json([
        "status" => "error",
        "data" => "Don't found saleID",
        "code" => 404,
      ]);
    }

    if (isset($data["saleID"])) {
      $this->saleID = $data["saleID"];

      if (isset($data["embarcacionesID"])) {
        $this->embarcacionesID = $data["embarcacionesID"];
      }

      if (isset($data["quantity"])) {
        $this->quantity = $data["quantity"];
      }

      $result = $this->updateSale();

      if ($result) {
        return response()->json([
          "status" => "success",
          "data" => $result,
          "code" => 200,
        ]);
      }

      if (!$result) {
        return response()->json([
          "status" => "Internal server error",
          "code" => 500,
        ]);
      }
    }
  }
  /**
   *
   */
  public function updateSale()
  {
    $query = "UPDATE sale SET ";

    if ($this->embarcacionesID !== "") {
      $query .= "embarcacionesID = '$this->embarcacionesID', ";
    }

    if ($this->quantity !== "") {
      $query .= "quantity = '$this->quantity', ";
    }

    if (strlen($query) > strlen("UPDATE Embarcaciones SET ")) {
      $query = substr($query, 0, strlen($query) - 2);
    }

    $query .= "WHERE saleID = $this->saleID";

    $result = parent::anyQuery($query);

    if ($result >= 1) {
      return $result;
    }

    if ($result < 1) {
      return false;
    }
  }

  public function delete($data)
  {
    if (!isset($data["saleID"])) {
      return response()->json([
        "status" => "error",
        "data" => "Don't found saleID",
        "code" => 40,
      ]);
    }

    if (isset($data["saleID"])) {
      $this->saleID = $data["saleID"];

      $result = $this->deleteSale();

      if ($result) {
        return response()->json([
          "status" => "success",
          "data" => $result,
          "code" => 200,
        ]);
      }

      if (!$result) {
        return response()->json([
          "status" => "Internal server error",
          "code" => 500,
        ]);
      }
    }
  }

  /**
   *
   */
  public function deleteSale()
  {
    $query = "DELETE FROM sale WHERE saleID = $this->saleID";

    $result = parent::anyQuery($query);

    if ($result >= 1) {
      return $result;
    }

    if ($result < 1) {
      return false;
    }
  }

  /**
   *
   */
  private function searchToken()
  {
    $query = "SELECT userID, token, status FROM users_token WHERE token = '$this->token' AND status = 'active'";
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
    $query = "UPDATE users_token SET date = '$date' WHERE id = $tokenId";

    $response = parent::anyQuery($query);

    if ($response >= 1) {
      return $response;
    }

    if ($response < 1) {
      return false;
    }
  }

  /**
   * @Route("/sales/{page}")
   * #Method({"GET"})
   * @param int $page
   * @return array
   * @throws Exception
   * @author SebastianDaza
   */
  public function getSalesAction(int $page = null): array
  {
    return $this->getListSales(
      $page,
      url("sales", "SalesController@getSalesAction")
    );
  }

  /**
   * @Route("/sales/{id}")
   * #Method({"GET"})
   * @param int $page
   * @return array
   * @throws Exception
   * @author SebastianDaza
   */
  public function getSaleAction(int $id = null): array
  {
    return $this->getSale(
      $id,
      url("sale", "SalesController@getSaleAction")
    );
  }

  /**
   *  @Route("/sales")
   *	@Method({"POST"})
   *	@return json
   *	@throws Exception
   */
  public function postSalesAction(): array
  {
    $post = input()->all();
    $url  = url("sales", "SalesController@postSalesAction");

    if (empty($post)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Do not send empty data",
        "instance" => $url,
      ]);
    }

    return $this->createSale($post, $url);
  }

  /**
   *
   */
  public function putSalesAction()
  {
    $object = input()->all();

    if (!empty($object)) {
      $result = $this->put($object);
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

  public function deleteSalesAction()
  {
    $object = input()->all();

    if (!empty($object)) {
      $result = $this->delete($object);
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

?>
