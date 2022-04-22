<?php

namespace Controller;

class ArticlesController extends Controller
{
    protected $modelName = "ArticleModel";

    public function index()
    {
        return ("Api introuvable, veuillez réessayer svp.");
    }

    public function getAllCategories()
    {
        echo (json_encode($this->model->getAll("", "categories")));
    }

    public function addArticle()
    {
        $args = array(
            'idUser' => FILTER_VALIDATE_INT,
            'idCategory' => FILTER_VALIDATE_INT,
            'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'smallDesc' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'tag' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        );
        $acceptedFile = $this->uploadFile();
        if ($acceptedFile){
            $data = filter_input_array(INPUT_POST,$args);
            $data["fileType"] = $acceptedFile;
            $data["filePath"] = $_FILES["file"]["name"];
            $this->model->insert($data);
            echo (json_encode("Article inséré correctement"));
        }else{
            echo (json_encode("Probleme avec le fichier."));
        }
    }

    public function getAllArticles(){
        echo(json_encode($this->model->getAll("INNER JOIN users ON users.idUser = articles.idUser INNER JOIN categories ON articles.idCategory = categories.idCategorie")));
    }

    public function searchArticles(){
        $query = "INNER JOIN users ON users.idUser = articles.idUser INNER JOIN categories ON articles.idCategory = categories.idCategorie ";
        $filter = [];
        $query1  ="";
        $where = false;
        $orderBy = false;
        if (isset($_GET["fileType"])){
            $filter['fileType'] = filter_input(INPUT_GET,"fileType",FILTER_DEFAULT);
            $query1 = "WHERE fileType='{$filter['fileType']}' ";
            $where = true;
        }
        if (isset($_GET["idCategory"])){
            $filter['idCategory'] = filter_input(INPUT_GET,"idCategory",FILTER_VALIDATE_INT);
            if(isset($filter['fileType'])){
                $query1 .= " AND idCategory= {$filter['idCategory']} ";
                $where = true;
            }else{
                $query1 = "WHERE idCategory= {$filter['idCategory']} ";
                $where = true;
            }
        }
        if (isset($_GET["text"])){
            $filter['filterByText'] = filter_input(INPUT_GET,"text",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if($where){
                $query1 .= "AND (title LIKE '%{$filter['filterByText']}%' OR smallDesc like '%{$filter['filterByText']}%')";
            }else{
                $query1 .= "WHERE title LIKE '%{$filter['filterByText']}%' OR smallDesc like '%{$filter['filterByText']}%'";
                $where = true;
            }
        }
        if (isset($_GET["tags"])){
            $filter['tags'] = filter_input(INPUT_GET,"tags",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if($where){
                $query1 .= "AND tag like '%{$filter['tags']}%'";
            }else{
                $query1 .= "WHERE tag like '%{$filter['tags']}%'";
                $where = true;
            }
        }
        if (isset($_GET["creationDate"])){
            $filter['creationDate'] = filter_input(INPUT_GET,"creationDate",FILTER_DEFAULT);
            $query1 .= " ORDER BY creationDate {$filter['creationDate']}";
            $orderBy = true;
        }
        if (isset($_GET["viewCount"])){
            $filter['viewCount'] = filter_input(INPUT_GET,"viewCount",FILTER_DEFAULT);
            if($orderBy){
                $query1 .= ", viewCount {$filter['viewCount']}";
            }else{
                $query1 .= " ORDER BY viewCount {$filter['viewCount']}";

            }
        }
            echo(json_encode($this->model->getAll($query . $query1)));
    }

    

    public function getArticleById(){
        $idArticle = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
        $query ="INNER JOIN users ON users.idUser = articles.idUser INNER JOIN categories ON articles.idCategory = categories.idCategorie WHERE idArticle = ".$idArticle;
        $allData = $this->model->getAll("WHERE idArticle = {$idArticle}");
        $data["viewCount"] = $allData[0]["viewCount"] + 1;
        $id["idArticle"] = $idArticle;
        $this->model->update($data,$id);
        echo(json_encode($this->model->getAll($query)));
    }

    public function getArticleByUserId(){
        if(isset($_SESSION["idUser"])){
            $idUser = filter_input(INPUT_GET,'idUser',FILTER_VALIDATE_INT);
            $query ="INNER JOIN users ON users.idUser = articles.idUser INNER JOIN categories ON articles.idCategory = categories.idCategorie WHERE articles.idUser = ".$idUser;
            echo(json_encode($this->model->getAll($query)));
        }else{
            echo(json_encode("Not Authorised."));
        }
    }

    public function updateView(){
        $idArticle = filter_input(INPUT_POST,"idArticle",FILTER_VALIDATE_INT);
        $id["idArticle"] = $idArticle;
        $allData = $this->model->getAll("WHERE idArticle = {$idArticle}");
        $data["viewCount"] = $allData[0]["viewCount"] + 1;
        echo json_encode($this->model->update($data,$id));
    }

    public function deleteArticle(){
        //il faudrait rajouter une vérification d'identité que l'article appartient réelement à l'utilisateur
        //pour le laisser effacer
        if(isset($_SESSION["idUser"])){
            $data["idArticle"] = filter_input(INPUT_GET,"idArticle",FILTER_VALIDATE_INT);
            echo json_encode($this->model->delete($data));
        }else{
            echo(json_encode("Not Authorised."));
        }
    }
}
