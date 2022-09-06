<?php
    use Ships\Router;
    include(__DIR__ . '../../App/Handlers/CustomExceptionHandler.php');
    
    Router::group(['exceptionHandler' => \Ships\Handlers\CustomExceptionHandler::class], function () {

        Router::get('APIRest-Back/', 'DefaultController@home')->name('home');
        
        Router::post('APIRest-Back/server/', 'ServiceController@register')->name('register');

        

        //Router::get('APIRest-Back/register/', 'ServiceController@showRegisterAction')->name('showRegisterAction');
    });

    Router::group(['exceptionHandler' => \Ships\Handlers\CustomExceptionHandler::class], function () {

        Router::get('APIRest-Back/ships/{id}', function ($shipId) {
            return "Your id is: " . $shipId;
        })->name('ships');

        Router::post('APIRest-Back/auth/', 'APIRestController@login')->name('login');
    }
    );
    
?>