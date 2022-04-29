<?php

namespace Controller;

abstract class Controller
{

    protected $modelName;
    protected $model;
    public function __construct()
    {
        $realModelName = "\\model\\" . $this->modelName;
        $this->model = new $realModelName;
    }

    public function uploadFile()
    {
        $file = $_FILES;
        if ($file["file"]["error"] == 0) {
            $tmpName = $file["file"]['tmp_name'];
            $name = $file["file"]['name'];
            $acceptedFile = (getimagesize($tmpName)) && $file["file"]['size'] < (1000000);
            if ($acceptedFile) {
                move_uploaded_file($tmpName, 'public/articlefile/' . $name);
                echo(json_encode("Upload Réussi"));
            }else{
                echo(json_encode("Echec de l'upload"));
            }
        }
    }
    public function uploadFileImg()
    {
        $file = $_FILES;
        if ($file["file"]["error"] == 0) {
            $tmpName = $file["file"]['tmp_name'];
            $name = $file["file"]['name'];
            $acceptedFile = (getimagesize($tmpName)) && $file["file"]['size'] < (6000000);
            if ($acceptedFile) {
                if(move_uploaded_file($tmpName, 'public/userprofile/' . $name)){
                    echo json_encode('upload effectué');
                }
                 
            }else{
                echo json_encode('probleme lors de upload');
            }
        }
    }
}
