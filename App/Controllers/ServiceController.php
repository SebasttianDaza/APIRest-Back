<?php

    namespace Ships\Controllers;

    
    use Pecee\Http\Input\InputHandler;
    use Ships\Controllers; 

    

    class ServiceController extends ConnectionController {

        public $data = [];

        /**
         * 
         */
        public function login($data) {

            if(!isset($data["username"]) || !isset($data["password"])) {
                response()->json([
                    'error' => 'Bad request',
                    'code'  => 400,
                ]);
            } else {
                $username = $data["username"];
                $password = $data["password"];
                //Encrypt password
                $password = parent::encrypt($password);

                $data = $this->getDataUsers($username);
                
                if ($data) {
                    $datauser = $data[0];
                    
                    if ($password == parent::encrypt($datauser["password"])) {
                        if ($datauser["status"] === "active") {
                            $token = $this->createToken($datauser["userID"]);
                            if ($token) {
                                response()->json([
                                    'status' => 'success',
                                    'data' => $datauser,
                                    'token' => $token
                                ]);
                            } else {
                                response()->json([
                                    'error' => 'Internal server error',
                                    'code'  => 500,
                                ]);
                            }                            
                        } 
                        
                        if ($datauser["status"] === "inactive") {
                            response()->json([
                                'error' => 'User is not active',
                                'code'  => 401,
                            ]);
                        }
                    }
                    
                    if ($password != parent::encrypt($datauser["password"])) {
                        response()->json([
                            'error' => 'Password incorrect',
                            'code'  => 401,
                        ]);
                    }

                } else  {
                    response()->json([
                        'error' => 'User not found',
                        'code'  => 404,
                    ]);
                }
            }
        }

        //Get data users for authentication
        /**
         * 
         */
        private function getDataUsers($username) {
            $query = "SELECT userID, password, status FROM users WHERE username = '$username'";
            $result = parent::getData($query);
            
            if (isset($result[0]["userID"])) {
                return $result;
            } else {
                return false;
            }
        }

        /**
         * 
         */
        private function createToken($userID) {
            $val = true;
            $token = bin2hex(openssl_random_pseudo_bytes(16, $val));
            $date = date("Y-m-d H:i:s");
            $state = "active";

            $query = "INSERT INTO users_token (userID, token, status, date) VALUES ($userID, '$token', '$state', '$date')";
            $verificate = parent::anyQuery($query);

            if ($verificate) {
                return $token;
            } else {
                return false;
            }
        } 

        /**
         * @Return json
         * @param json
         * Insert user to database
         */
        public function register() : string {
            $object = input()->all();

            if(!empty($object)) {
                $this->data = $object;

                $query = 'INSERT INTO users (username, email, password, status) VALUES ("' . $this->data["username"] . '", "' . $this->data["email"] . '", "' . $this->data["password"] . '", "active")';
                
                
                $response = parent::anyQuery($query);

                if ($response >= 1) {
                    return response()->json([
                        'message' => 'User created successfully',
                        'code' => 200
                    ]);
                } 
                
                if ($response == 0) {
                    return response()->json([
                        'message' => 'User not created',
                        'code' => 500
                    ]);
                }
                
            } else {
                return response()->json([
                    'message' => 'User not created',
                    'code' => 500
                ]);
            }
            
        }

        /**
         * 
         */
        public function authAction() : string {
            $object = input()->all();
            $response = $this->login($object);

            return $response;
        }
    }
    
?>