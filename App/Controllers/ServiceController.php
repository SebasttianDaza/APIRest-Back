<?php

    namespace Ships\Controllers;

    
    use Pecee\Http\Input\InputHandler;
    use Ships\Controllers; 

    include(__DIR__ . "./ConnectionController.php");

    class ServiceController {

        public $data = [];

        /**
         * 
         */
        public function register() : string {
            $object = input()->all();

            if(!empty($object)) {
                $this->data = $object;

                $query = 'INSERT INTO users (username, email, password, status) VALUES ("' . $this->data["username"] . '", "' . $this->data["email"] . '", "' . $this->data["password"] . '", "active")';
                
                $connection = new ConnectionController();
                $response = $connection->anyQuery($query);

                $response = [
                    'status' => 'success',
                    'data' => $this->data,
                    'count' => $response
                ];
                

                return json_encode($response);
            } else {
                $response = [
                    'status' => 'error',
                    'data' => 'No data found'
                ];
                return json_encode($response);
            }
            
        }

        /**
         * 
         */
        public function showRegisterAction() : string {
            if(isset($this->data)) {
                return var_dump($this->data);
            }
            return 'No data';
        }
    }
    
?>