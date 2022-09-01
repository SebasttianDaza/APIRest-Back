<?php
    use Ships\Router;
    include(__DIR__ . '../../App/Handlers/CustomExceptionHandler.php');
    
    Router::group(['exceptionHandler' => \Ships\Handlers\CustomExceptionHandler::class], function () {
        Router::get('APIRest-Back/', 'DefaultController@home')->name('home');

    })
    
?>