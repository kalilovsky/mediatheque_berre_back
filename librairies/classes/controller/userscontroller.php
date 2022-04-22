<?php
namespace Controller;

class UsersController extends Controller{
    protected $modelName = "UsersModel";

    public function login(){
        $response = json_encode($this->model->loginUser($_POST));
        setcookie('userInfo',$response,time()+(60*30),'/');
        echo $response;
    }
    public function register(){
       echo json_encode($this->model->registerUser($_POST));
    }
    public function updateUser(){
        $args = array(
            'pseudo' => FILTER_SANITIZE_SPECIAL_CHARS,
            'email' => FILTER_VALIDATE_EMAIL,
            'firstname' => FILTER_SANITIZE_SPECIAL_CHARS,
            'lastname' => FILTER_SANITIZE_SPECIAL_CHARS
        );
        $data = filter_input_array(INPUT_POST,$args);
        $idUser["idUser"] = filter_input(INPUT_POST,"idUser",FILTER_VALIDATE_INT);
        if(!empty($_FILES["file"]["name"])){
            $data["profilPhoto"] = $_FILES["file"]["name"];
        }
        if(!empty($_POST["pwd"])){
          $data["pwd"] = password_hash(filter_input(INPUT_POST,"pwd",FILTER_DEFAULT), PASSWORD_DEFAULT);
        }
        $this->uploadFileImg();
        $this->model->update($data,$idUser);
        $response = ($this->model->getAll("WHERE idUser=".$idUser["idUser"]))[0];
        $response["isConnected"] = true;
        echo json_encode($response);
    }
    public function disconnect(){
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
            setcookie('userInfo', '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_unset();
        session_destroy();
        echo "disconnected";
    }

}