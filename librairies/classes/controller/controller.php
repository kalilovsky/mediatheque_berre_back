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
            $allowed_ext = array('avi', 'flv', 'wmv', 'mov', 'mp4','mpg');
            $isVideo = in_array(end(explode(".", $name)), $allowed_ext);
            $acceptedFile = (getimagesize($tmpName) || $isVideo) && $file["file"]['size'] < (6000000);
            if ($acceptedFile) {
                move_uploaded_file($tmpName, 'public/articlefile/' . $name);
                if ($isVideo) {
                    return "video";
                } else {
                    return "image";
                }
            }else{
                return false;
            }
        }
    }public function uploadFileImg()
    {
        $file = $_FILES;
        if ($file["file"]["error"] == 0) {
            $tmpName = $file["file"]['tmp_name'];
            $name = $file["file"]['name'];
            $acceptedFile = (getimagesize($tmpName)) && $file["file"]['size'] < (6000000);
            if ($acceptedFile) {
                return move_uploaded_file($tmpName, 'public/userprofile/' . $name);
            }else{
                return false;
            }
        }
    }
}
