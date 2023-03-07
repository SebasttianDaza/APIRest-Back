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
   * @param array $data
   * @param string $url
   * @return array
   */
  private function updateSale(array $data, string $url = null): array
  {
    if (
      !array_key_exists("saleID", $data) ||
      empty($data["saleID"]) ||
      !is_numeric($data["saleID"]) ||
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

    $this->saleID = $data["saleID"];
    unset($data["saleID"]);

    $response = parent::updateItem("sale", $data, $this->saleID, "saleID");


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
      "id" => $this->saleID,
      "Sale" => $response,
      "detail" => "Sale updated",
      "instance" => $url,
    ]);
  }

  /**
   * @param int $id
   * @param string $url
   * @return array 
   */
  private function deleteSale(int $id, string $url = null): array
  {
    $response = parent::removeItem("sale", $id, "saleID");

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
      "Sale" => $response,
      "detail" => "Sale deleted",
      "instance" => $url ?? null,
    ]);
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
   *  @Route("/sales")
   * @Method({"PUT})
   * @return json
   */
  public function putSalesAction(): array
  {
    $put = input()->all();
    $url = url("sales", "SalesController@putSalesAction");

    if (empty($put)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Do not send empty data",
        "instance" => $url,
      ]);
    }

    return $this->updateSale($put, $url);
  }

  
   /**
   *  @Route("/sale/{$id}")
   * @Method({"DELETE"})
   * @return json
   */
  public function deleteSaleAction(int $id = null): array
  {
    $url = url("sale", "SalesController@deleteSaleAction");

    if (empty($id)) {
      response()->httpCode(400);
      return response()->json([
        "StatusMsg" => "Bad request",
        "StatusCode" => 400,
        "detail" => "Do not send empty data",
        "instance" => $url,
      ]);
    }
    
    return self::deleteSale($id, $url);
  }
}

?>
