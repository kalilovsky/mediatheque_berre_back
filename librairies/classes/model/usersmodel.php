<?php

namespace Model;

class UsersModel extends Manager
{

    protected $table = "users";

    public function loginUser($userInfo)
    {
        $db = $this->dbConnect();

        $selectSql = "SELECT * FROM users WHERE email = :email";
        $querySql = $db->prepare($selectSql);
        $querySql->execute([
            "email" => $userInfo["email"]
        ]);
        if ($querySql->rowCount() > 0) {
            $resultSql = $querySql->fetch();
            $verifPwd = password_verify($userInfo["pwd"], $resultSql["pwd"]);
            if ($verifPwd) {
                $this->setSession($resultSql);
                return $_SESSION;
            } else {
                $state = [];
            $state["isConnected"]=false;
            $state["messageLogin"]="Mdp incorrect.";
            return $state;
               
            }
        } else {
            $state = [];
            $state["isConnected"]=false;
            $state["messageLogin"]="Utilisateur non présent.";
            return $state;
        }
    }

    public function registerUser($userInfo)
    {
        if (!$this->verifyUserData($userInfo)) {
            $state = [];
            $state["isConnected"]=false;
            $state["messageRegister"]="Erreur dans les données saisies";
            return $state;
            
        }

        $db = $this->dbConnect();
        $selectSql = "SELECT * FROM users WHERE email = :email OR pseudo = :Pseudo";
        $querySql = $db->prepare($selectSql);
        $querySql->execute([
            "email" => $userInfo["email"],
            "Pseudo" => $userInfo["pseudo"]
        ]);
        if (!$querySql->rowCount() > 0) {
            $pwd = password_hash($userInfo["pwd"], PASSWORD_DEFAULT);
            $insertSql = "INSERT INTO users(firstname,lastname,email,pwd,pseudo) VALUES (:firstname,:lastname,:email,:pwd,:pseudo)";
            $querySql = $db->prepare($insertSql);
            $querySql->execute([
                "firstname" => $userInfo["firstname"],
                "lastname" => $userInfo["lastname"],
                "email" => $userInfo["email"],
                "pwd" => $pwd,
                "pseudo" => $userInfo["pseudo"]
            ]);
            $test = $querySql->errorInfo();
            $userInfo["pwd"] = $pwd;
            $userInfo["usertype"] = "normal";
            $userInfo["profilPhoto"] = "account_default.png";
            $userInfo["idUser"] = $db->lastInsertId();
            $this->setSession($userInfo);
            return $_SESSION;
        } else {
            $state = [];
            $state["isConnected"]=false;
            $state["messageRegister"]="Utilisateur déja présent.";
            return $state;
        }
    }

    private function setSession($resultSql)
    {
        
        //session_start();
        $_SESSION["email"] = $resultSql["email"];
        $_SESSION["firstname"] = $resultSql["firstname"];
        $_SESSION["lastname"] = $resultSql["lastname"];
        $_SESSION["photo"] = $resultSql["profilPhoto"];
        $_SESSION["idUser"] = $resultSql["idUser"];
        $_SESSION["userType"] = $resultSql["usertype"];
        // $_SESSION["pwd"] = $resultSql["pwd"];
        $_SESSION["pseudo"] = $resultSql["pseudo"];
        $_SESSION["isConnected"]=true;
        $_SESSION["message"]="Users well registred";
    }

    public function verifyUserData($userInfo)
    {
        //vérifie que 
        foreach ($userInfo as $elem) {
            if (empty($elem)) {
                return false;
            }
        }
        if ($userInfo["pwd"] != $userInfo["pwd2"]) {
            return false;
        }
        return true;
    }

    
}
