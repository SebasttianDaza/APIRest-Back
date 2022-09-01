<?php
    
    namespace Ships;

    include(__DIR__ . './App/Controllers/DefaultController.php');    

    use Pecee\SimpleRouter\SimpleRouter;

    class Router extends SimpleRouter
    {
        /**
         * @throws \Exception
         * @throws \Pecee\Http\Middleware\Exceptions\TokenMismatchException
         * @throws \Pecee\SimpleRouter\Exceptions\HttpException
         * @throws \Pecee\SimpleRouter\Exceptions\NotFoundHttpException
         */
        public static function start(): void
        {
            // Load our helpers
            require_once(__DIR__ . './helpers.php');

            parent::setDefaultNamespace('Ships\Controllers');
            

            // Load our custom routes
            require_once(__DIR__ . './routes/web.php');

            // Do initial stuff
            parent::start();
        }
    }

?>