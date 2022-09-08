<?php 
    namespace Ships\Controllers;

    use Ships\Controllers;

    class UsersController extends ConnectionController {
        private $userID;
        private $username = "";
        private $lastname = "";
        private $email = "";
        private $embarcacionesID = "";
        private $token = "";

        /**
         * 
         */
        public function getList($page = 1) {
            $start = 0;
            $count = 100;

            if ($page > 1) {
                $start = ($count * ($page - 1)) + 1;
                $count = $count * $page;
            }

            $query = "SELECT * FROM usuarios LIMIT $start, $count";
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
        public function getUser($id = 0) {
            $query = "SELECT * FROM usuarios WHERE userID = $id";
            $result = parent::getData($query);

            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'data' => $result,
                    'code' => 200
                ]);
            }

            if (!$result) {
                return response->json([
                    'status' => 'error',
                    'data' => 'Don\'t exist user',
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
                    'data' => 'Don\'t exist token',
                    'code' => 401
                ]);
            }

            if (isset($data["token"])) {
                $this->token = $data["token"];

                $dataToken = $this->searchToken();

                if ($dataToken) {
                    if (    !isset($data["username"])
                        ||  !isset($data["lastname"])
                        ||  !isset($data["email"])
                        ||  !isset($data["embarcacionesID"])
                    ) {
                        return response()->json([
                            'status' => 'error',
                            'data' => 'Don\'t exist data',
                            'code' => 400
                        ]);
                    } else {
                        $result = $this->insertUser($data);

                        if ($result) {
                            return response->json([
                                'status' => 'success',
                                'data' => $result,
                                'code' => 200
                            ]);
                        }

                        if (!$result) {
                            return response()->json([
                                'status' => 'error',
                                'data' => 'Internal server error',
                                'code' => 500
                            ]);
                        }
                    }
                }

                if (!$dataToken) {
                    return response->json([
                        'status' => 'error',
                        'data' => 'Unauthorized',
                        'code' => 401
                    ]);
                }
            }

        }

        /**
         * 
         */
        public function insertUser($data) {
            $this->username = $data["username"];
            $this->lastname = $data["lastname"];
            $this->email = $data["email"];
            $this->embarcacionesID = $data["embarcacionesID"];

            $query = "INSERT INTO usuarios (username, lastname, email, embarcacionesID) VALUES ('$this->username', '$this->lastname', '$this->email', '$this->embarcacionesID')";
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
            
            if (!isset($data["userID"])) {
                return response()->json([
                    'status' => 'error',
                    'data' => 'Don\'t exist userID',
                    'code' => 400
                ]);
            }

            if (isset($data["userID"])) {
                $this->userID = $data["userID"];

                if (isset($data["username"])) {
                    $this->username = $data["username"];
                }

                if (isset($data["lastname"])) {
                    $this->lastname = $data["lastname"];
                }

                if (isset($data["email"])) $this->email = $data["email"];

                if (isset($data["embarcacionesID"])) {
                    $this->embarcacionesID = $data["embarcacionesID"];
                }

                $result = $this->updateUser();

                if ($result) {
                    return response->json([
                        'status' => 'success',
                        'data' => $result,
                        'code' => 200
                    ]);
                }

                if (!$result) {
                    return response()->json([
                        'status' => 'error',
                        'data' => 'Internal server error',
                        'code' => 500
                    ]);
                }
            }
        }

        /**
         * 
         */
        public function updateUser() {
            $query = "UPDATE usuarios SET";

            if ($this->username !== "") {
                $query .= " username = '$this->username', ";
            }

            if ($this->lastname !== "") {
                $query .= " lastname = '$this->lastname', ";
            }

            if ($this->email !== "") {
                $query .= " email = '$this->email', ";
            }

            if ($this->embarcacionesID !== "") {
                $query .= " embarcacionesID = '$this->embarcacionesID', ";
            }

            //Si solo es uno, quita la ultima coma
            if (strlen($query) > strlen("UPDATE Embarcaciones SET ")) {
                $query = substr($query, 0, strlen($query) - 2);
            }

            $query .= " WHERE userID = $this->userID";

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
        public function delete($data) {

            if (!isset($data["userID"])) {
                return response()->json([
                    'status' => 'error',
                    'data' => 'Don\'t exist userID',
                    'code' => 400
                ]);
            }

            if (isset($data["userID"])) {
                $this->userID = $data["userID"];

                $result = $this->deleteUser();

                if ($result) {
                    return response()->json([
                        'status' => 'success',
                        'data' => [
                            'userID' => $this->userID,
                            'count' => $result
                        ],
                        'code' => 200
                    ]);
                }

                if (!$result) {
                    return response->json([
                        'status' => 'error',
                        'data' => 'Internal server error',
                        'code' => 500
                    ]);
                }

            }
        }

        /**
         * 
         */
        public function deleteUser() {
            $query = "DELETE FROM usuarios WHERE userID = $this->userID";
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
        public function getUsersAction($page = null) : String {
            $result = $this->getList($page);
            return $result;
        }

        /**
         * 
         */
        public function getUserAction($id) : String {
            $result = $this->getSale($id);
            return $result;
        }

        /**
         * 
         */
        public function postUsersAction() {
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
        public function putUsersAction() {
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

        public function deleteUsersAction() {
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