<?php 
    namespace Ships\Controllers;

    use Pecee\Http\Input\InputHandler;
    require_once(__DIR__ . "../../../Class/Routes/Request.class.php");

    class APIRestController {

        public function getEmbarcacion($id) : string {
            return "Your id is: " . $id;
        }

        public function login () : string {
            $object = input()->all();

            
            
        }   
    }
?>