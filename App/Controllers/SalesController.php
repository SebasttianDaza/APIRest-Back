<?php 
    namespace Ships\Controllers;

    use Ships\Controllers;

    class SalesController extends ConnectionController {
        private $saleID;
        private $embarcacionesID = "";
        private $quantity = "";

        /**
         * 
         */
        public function getList($page = 1) 
        {
            $start = 0;
            $count = 100;

            if ($page > 1) {
                $start = ($count * ($page - 1)) + 1;
                $count = $count * $page;
            }

            $query = "SELECT * FROM sale LIMIT $start, $count";
            $result = parent::getData($query);

            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'data' => $result,
                    'code' => 200
                ]);
            }

            if (!$result) {
                return response()->json([
                    'status' => 'error',
                    'data' => 'Don\'t exist sales',
                    'code' => 404
                ]);
            }
        }

        /**
         * 
         */
        public function getSale($id = 0)
        {
            $query = "SELECT * FROM sale WHERE saleID = $id";
            $result = parent::getData($query);

            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'data' => $result,
                    'code' => 200
                ]);
            }

            if (!$result) {
                return response()->json([
                    'status' => 'error',
                    'data' => 'Don\'t found sale',
                    'code' => 404
                ]);
            }    
        }

        /**
         * 
         */
        public function post($data) {

            if (!isset($data["token"])) {
                return response()->json([
                    'status' => 'error',
                    'data' => "Don't found token",
                    'code' => 404
                ]);
            }

            if (isset($data["token"])) {
                $this->token = $data["token"];

                $dataToken = $this->searchToken();

                if ($dataToken) {
                    if (!isset($data["embarcacionesID"])
                        ||!isset($data["quantity"])
                    ) {
                        return response()->json([
                            'status' => 'error',
                            'data' => "Don't found data",
                            'code' => 404
                        ]);
                    } else {
                        $result = $this->insertSale($data);

                        if ($result) {
                            return response()->json([
                                'status' => 'success',
                                'data' => $result,
                                'code' => 200
                            ]);
                        }

                        if (!$result) {
                            return response()->json([
                                'status' => 'error',
                                'data' => 'Could not insert the sale',
                                'code' => 500
                            ]);
                        }
                    }
                }

                if (!$dataToken) {
                    return response()->json([
                        'status' => 'Internal server error',
                        'code' => 500
                    ]);
                }
            }
        }

        /**
         * 
         */
        public function insertSale($data) {
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
        public function put($data) {

            if (!isset($data["saleID"])) {
                return response()->json([
                    'status' => 'error',
                    'data' => "Don't found saleID",
                    'code' => 404
                ]);
            }

            if (isset($data["saleID"])) {
                $this->saleID = $data["saleID"];

                if (isset($data["embarcacionesID"])) {
                    $this->embarcacionesID = $data["embarcacionesID"];
                }

                if (isset($data["quantity"])) $this->quantity = $data["quantity"];

                $result = $this->updateSale();

                if ($result) {
                    return response()->json([
                        'status' => 'success',
                        'data' => $result,
                        'code' => 200
                    ]);
                }

                if (!$result) {
                    return response()->json([
                        'status' => 'Internal server error',
                        'code' => 500
                    ]);
                }

            }
        }
        /**
         * 
         */
        public function updateSale() {
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

        public function delete($data) {

            if (!isset($data["saleID"])) {
                return response()->json([
                    'status' => 'error',
                    'data' => "Don't found saleID",
                    'code' => 40
                ]);
            }

            if (isset($data["saleID"])) {
                $this->saleID = $data["saleID"];

                $result = $this->deleteSale();

                if ($result) {
                    return response()->json([
                        'status' => 'success',
                        'data' => $result,
                        'code' => 200
                    ]);
                }

                if (!$result) {
                    return response()->json([
                        'status' => 'Internal server error',
                        'code' => 500
                    ]);
                }
            }
        }

        /**
         * 
         */
        public function deleteSale() {
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
        private function searchToken() {
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
        private function updateToken($tokenId) {
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
         * 
         */
        public function getSalesAction($page = null) : String {
            $result = $this->getList($page);
            return $result;
        }

        /**
         * 
         */
        public function getSaleAction($id) : String {
            $result = $this->getSale($id);
            return $result;
        }

        /**
         * 
         */
        public function postSalesAction() {
            $object = input()->all();

            if(!empty($object)) {
               $result = $this->post($object);
                return $result;
                
            } else {
                return response()->json([
                    "status" => "error",
                    "code" => 400,
                    "message" => "No se ha recibido ningun dato"
                ]);
            }
        }

        /**
         * 
         */
        public function putSalesAction() {
            $object = input()->all();

            if(!empty($object)) {
                $result = $this->put($object);
                return $result;
            } else {
                return response()->json([
                    "status" => "error",
                    "code" => 400,
                    "message" => "No se ha recibido ningun dato"
                ]);
            }
        }

        /**
         * 
         */

        public function deleteSalesAction() {
            $object = input()->all();

            if(!empty($object)) {
                $result = $this->delete($object);
                return $result;
            } else {
                return response()->json([
                    "status" => "error",
                    "code" => 400,
                    "message" => "No se ha recibido ningun dato"
                ]);
            }
        }

    }

?>