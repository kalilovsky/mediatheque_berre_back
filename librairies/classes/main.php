<?php

use Controller\Controller;

class Main {

    const defaultController = "ArticlesController";
    const defaultAction = "index";

    public static function start(){
        session_start();
        $controllerName = self::getControllerName();
        $actionName = self::getActionName();
        $controller = new $controllerName;
        $controller->$actionName();
    }

    private static function getActionName(){
        $actionName = filter_input(INPUT_POST,"action",FILTER_SANITIZE_SPECIAL_CHARS);
        
        if(!$actionName){
            $actionName = filter_input(INPUT_GET,"action",FILTER_SANITIZE_SPECIAL_CHARS);
        }

        if(!$actionName){
            $actionName = self::defaultAction;
        }

        return $actionName;
    }

    private static function getControllerName(){
        $controllerName = filter_input(INPUT_POST,"controller",FILTER_SANITIZE_SPECIAL_CHARS);
        
        if(!$controllerName){
            $controllerName = filter_input(INPUT_GET,"controller",FILTER_SANITIZE_SPECIAL_CHARS);
        }
        if(!$controllerName){
            $controllerName = self::defaultController;
        }
        return "Controller\\" . $controllerName;
    }
}