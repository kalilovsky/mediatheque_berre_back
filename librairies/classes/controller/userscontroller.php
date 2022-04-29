<?php
namespace Controller;

class UsersController extends Controller{
    protected $modelName = "UsersModel";

    public function login(){
        $response = json_encode($this->model->loginUser($_POST));
        // $cookie_options = array(
        //     'expires' => time() + 60*30,
        //     'path' => '/',
        //     // 'domain' => '.domain.com', // leading dot for compatibility or use subdomain
        //     'secure' => false, // or false
        //     'httponly' => false, // or false
        //     'samesite' => 'None' // None || Lax || Strict
        //   );
        setcookie('userInfo',$response,time()+(60*30),'/');
        // setcookie('userInfo',$response,$cookie_options);
        echo $response;
    }
    
    public function register(){
        $response = json_encode($this->model->registerUser($_POST));
        setcookie('userInfo',$response,time()+(60*30),'/');
        echo $response;
    }

    public function addUser(){
        echo json_encode($this->model->registerUser($_POST,true));
    }
    public function updateUserSettings(){
        $args = array(
            'firstname' => FILTER_VALIDATE_BOOLEAN,
            'lastname' => FILTER_VALIDATE_BOOLEAN,
            'email' => FILTER_VALIDATE_BOOLEAN,
            'pseudo'  => FILTER_VALIDATE_BOOLEAN,
            'pwd' => FILTER_VALIDATE_BOOLEAN,
            'profilPhoto'  => FILTER_VALIDATE_BOOLEAN,
            'adresse' => FILTER_VALIDATE_BOOLEAN,
            'telephone' => FILTER_VALIDATE_BOOLEAN,
        );
        $data = filter_input_array(INPUT_POST, $args);
        echo (json_encode($this->model->updateSet($data,array('1'=>'1'),'usersettings')));
    }

   

    public function getUserSetting(){
        echo (json_encode($this->model->getAll("","usersettings")[0]));
    }

    public function updateUser(){
        $args = array(
            'pseudo' => FILTER_DEFAULT,
            'email' => FILTER_VALIDATE_EMAIL,
            'firstname' => FILTER_DEFAULT,
            'lastname' => FILTER_DEFAULT,
            'adresse' => FILTER_DEFAULT,
            'telephone' => FILTER_DEFAULT,
            'profilPhoto' => FILTER_DEFAULT,
        );
        $data = filter_input_array(INPUT_POST,$args);
        $idUser["idUser"] = filter_input(INPUT_POST,"idUser",FILTER_VALIDATE_INT);
        // if(!empty($_FILES["file"]["name"])){
        //     $data["profilPhoto"] = $_FILES["file"]["name"];
        // }
        if(!empty($_POST["pwd"])){
          $data["pwd"] = password_hash(filter_input(INPUT_POST,"pwd",FILTER_DEFAULT), PASSWORD_DEFAULT);
        }
        // $this->uploadFileImg();
        $this->model->update($data,$idUser);
        $response = ($this->model->getAll("WHERE idUser=".$idUser["idUser"]))[0];
        $response["isConnected"] = true;
        echo json_encode($response);
    }

    public function deleteUser(){
        $idUser["idUser"] = filter_input(INPUT_POST,"idUser",FILTER_VALIDATE_INT);
        echo json_encode($this->model->delete($idUser));
    }

    public function getCountUsers(){
        echo(json_encode($this->model->countAll()));
    }

    public function  getAllUsers(){
        echo(json_encode($this->model->getAll('ORDER BY idUser DESC')));
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
        echo json_encode("disconnected");
    }

}