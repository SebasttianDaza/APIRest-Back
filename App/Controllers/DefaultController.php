<?php

    namespace Ships\Controllers;
    require_once(__DIR__ . "../../../vendor/autoload.php");
    use Dotenv;
    

    class DefaultController
    {
        public function home(): string
        {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, "../../../.env");
            $dotenv->safeLoad();
            return redirect($_ENV["URL_HOME"], 301);
        }

        public function contact(): string
        {
            return 'DefaultController -> contact';
        }

    }
