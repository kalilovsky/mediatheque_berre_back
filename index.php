<?php
header('Access-Control-Allow-Origin: http://localhost:8080');
// header('Access-Control-Allow-Origin: http://localhost:8081');
// header('Access-Control-Allow-Origin: http://localhost:46059/');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');


require_once("librairies/autoload.php");

session_start();
    
Main::start();