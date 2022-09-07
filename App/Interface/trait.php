<?php 
    namespace Ships\Trait;

    trait httpRequest {
        public function httpResponseCode($code = 200) {
            return http_response_code($code);
        } 
    }


?>