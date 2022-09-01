<?php
    error_reporting(E_ERROR);
    //Traer vendor/autoload.php
    require_once '../vendor/autoload.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Content-Type: application/json; charset=utf-8");


    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, "../.env");
    $dotenv->safeLoad();

    $json = file_get_contents('php://input');
    $jsonObj = json_decode($json);

    // Use $jsonObj
    print_r($jsonObj);
