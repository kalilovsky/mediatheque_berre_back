<?php
// header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
// header('Access-Control-Allow-Origin: http://192.168.1.27:8080');
header('Access-Control-Allow-Origin: http://localhost:8080');
// header('Access-Control-Allow-Origin: http://localhost:8081');
// header('Access-Control-Allow-Origin: http://localhost:46059/');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');


require_once("librairies/autoload.php");

    
Main::start();