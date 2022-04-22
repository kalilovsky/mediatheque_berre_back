<?php

spl_autoload_register(function($className){
    $realPath ='librairies/classes/';
    $className = strtolower(str_replace('\\','/',$className));
    require_once($realPath . $className . '.php');
});