<?php

    namespace Ships\Interfaces;

    interface ReturnJson {
        public function returnResponseJSON($response);
    }
 
    interface IRequest extends ReturnJson {
        public function success_200($result);
        public function error_405();
        public function error_200($string = "Data incorrect");
        public function error_400();
        public function error_404();
        public function error_500();
    }

    interface IConnection {
        public function getData($query);
        public function showError($e);
    }

    interface RequestClass extends ReturnJson {
        public function getList($page = 1);
        public function post($json);
        public function put($json);
        public function delete($id);
    }
?>