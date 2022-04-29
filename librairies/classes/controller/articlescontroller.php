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

    public function getAllSubCategories()
    {
        if (isset($_POST["idCategorie"]) && ($_POST["idCategorie"] !== "")) {
            $idCategorie = filter_input(INPUT_POST, "idCategorie", FILTER_VALIDATE_INT);
            $condition = "WHERE idCategorie = " . $idCategorie;
        } else {
            $condition = "";
        }
        echo (json_encode($this->model->getAll($condition, "subcategories")));
    }

    public function getAllCollections()
    {
        echo (json_encode($this->model->getAll("", "collections")));
    }

    public function getAllAuthors()
    {
        echo (json_encode($this->model->getDistinctAll("author")));
    }
    public function getAllEditors()
    {
        echo (json_encode($this->model->getDistinctAll("editor")));
    }

    
    public function addArticle()
    {
        if(isset($_SESSION["idUser"])){
            $idUser = $_SESSION["idUser"];
            $args = array(
                'idCategory' => FILTER_VALIDATE_INT,
                'title' => FILTER_DEFAULT,
                'smallDesc' => FILTER_DEFAULT,
                'filePath' => FILTER_DEFAULT,
                'author'  => FILTER_DEFAULT,
                'stock' => FILTER_VALIDATE_INT,
                'editor'  => FILTER_DEFAULT,
                'loanDuration' => FILTER_VALIDATE_INT,
                'idSubCategorie' => FILTER_VALIDATE_INT,
                'idCollection' => FILTER_VALIDATE_INT,
                'format'  => FILTER_DEFAULT,
                'datePublished'=>FILTER_DEFAULT
            );
            $data = filter_input_array(INPUT_POST, $args);
            $data['idUser'] = $idUser;
            $this->model->insert($data);
            echo (json_encode("Article inséré correctement"));
        }else{
            echo(json_encode("Not Authorised."));
        }
    }

    public function getAllArticles()
    {
        echo (json_encode($this->model->getAll("INNER JOIN users ON users.idUser = articles.idUser INNER JOIN categories ON articles.idCategory = categories.idCategorie INNER JOIN subcategories ON subcategories.idSubCategorie = articles.idSubCategorie INNER JOIN collections ON collections.idCollection = articles.idCollection ORDER BY idArticle DESC")));
    }

    public function updateArticleSettings(){
        $args = array(
            'title' => FILTER_VALIDATE_BOOLEAN,
            'smallDesc' => FILTER_VALIDATE_BOOLEAN,
            'filePath' => FILTER_VALIDATE_BOOLEAN,
            'author'  => FILTER_VALIDATE_BOOLEAN,
            'stock' => FILTER_VALIDATE_BOOLEAN,
            'editor'  => FILTER_VALIDATE_BOOLEAN,
            'loanDuration' => FILTER_VALIDATE_BOOLEAN,
            'idCategory' => FILTER_VALIDATE_BOOLEAN,
            'idSubCategorie' => FILTER_VALIDATE_BOOLEAN,
            'idCollection' => FILTER_VALIDATE_BOOLEAN,
            'idUser'=> FILTER_VALIDATE_BOOLEAN,
            'format'  => FILTER_VALIDATE_BOOLEAN,
            'datePublished'=>FILTER_VALIDATE_BOOLEAN,
        );
        $data = filter_input_array(INPUT_POST, $args);
        echo (json_encode($this->model->updateSet($data,array('1'=>'1'),'articlesettings')));
    }

    public function getArticleSetting(){
        echo (json_encode($this->model->getAll("","articlesettings")[0]));
    }

    public function searchArticles()
    {
        $query = "INNER JOIN users ON users.idUser = articles.idUser INNER JOIN categories ON articles.idCategory = categories.idCategorie INNER JOIN collections ON collections.idCollection = articles.idCollection INNER JOIN subcategories ON subcategories.idSubCategorie = articles.idSubCategorie ";
        $filter = [];
        $query1  = "";
        $where = false;
        $orderBy = false;
        if (isset($_POST["idCategory"]) && (!empty($_POST["idCategory"]))) {
            $filter['idCategory'] = filter_input(INPUT_POST, "idCategory", FILTER_VALIDATE_INT);
            if ($where) {
                $query1 .= " AND idCategory= {$filter['idCategory']} ";
                $where = true;
            } else {
                $query1 = "WHERE idCategory= {$filter['idCategory']} ";
                $where = true;
            }
        }
        if (isset($_POST["idSubCategorie"]) && (!empty($_POST["idSubCategorie"]))) {
            $filter['idSubCategorie'] = filter_input(INPUT_POST, "idSubCategorie", FILTER_VALIDATE_INT);
            if ($where) {
                $query1 .= " AND articles.idSubCategorie= {$filter['idSubCategorie']} ";
                $where = true;
            } else {
                $query1 = "WHERE articles.idSubCategorie= {$filter['idSubCategorie']} ";
                $where = true;
            }
        }
      if (isset($_POST["idCollection"]) && (!empty($_POST["idCollection"]))) {
            $filter['idCollection'] = filter_input(INPUT_POST, "idCollection", FILTER_VALIDATE_INT);
            if ($where) {
                $query1 .= " AND articles.idCollection= {$filter['idCollection']} ";
                $where = true;
            } else {
                $query1 = "WHERE articles.idCollection= {$filter['idCollection']} ";
                $where = true;
            }
        }
        if (isset($_POST["author"]) && (!empty($_POST["author"]))) {
            $filter['author'] = filter_input(INPUT_POST, "author", FILTER_DEFAULT);
            if ($where) {
                $query1 .= " AND author= '{$filter['author']}' ";
                $where = true;
            } else {
                $query1 = "WHERE author= '{$filter['author']}' ";
                $where = true;
            }
        }
        if (isset($_POST["editor"]) && (!empty($_POST["editor"]))) {
            $filter['editor'] = filter_input(INPUT_POST, "editor", FILTER_DEFAULT);
            if ($where) {
                $query1 .= " AND editor= '{$filter['editor']}' ";
                $where = true;
            } else {
                $query1 = "WHERE editor= '{$filter['editor']}' ";
                $where = true;
            }
        }

        if (isset($_POST["text"])) {
            $filter['filterByText'] = filter_input(INPUT_POST, "text", FILTER_DEFAULT);
            if ($where) {
                $query1 .= "AND (title LIKE '%{$filter['filterByText']}%' OR smallDesc like '%{$filter['filterByText']}%')";
            } else {
                $query1 .= "WHERE title LIKE '%{$filter['filterByText']}%' OR smallDesc like '%{$filter['filterByText']}%'";
                $where = true;
            }
        }
        echo (json_encode($this->model->getAll($query . $query1)));
    }

    public function getCountArticles(){
        echo(json_encode($this->model->countAll()));
    }


    public function getArticleById()
    {
        $idArticle = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $query = "INNER JOIN users ON users.idUser = articles.idUser INNER JOIN categories ON articles.idCategory = categories.idCategorie WHERE idArticle = " . $idArticle;
        $allData = $this->model->getAll("WHERE idArticle = {$idArticle}");
        $data["viewCount"] = $allData[0]["viewCount"] + 1;
        $id["idArticle"] = $idArticle;
        $this->model->update($data, $id);
        echo (json_encode($this->model->getAll($query)));
    }

    public function updateArticle(){
        $args = array(
            'idCategory' => FILTER_VALIDATE_INT,
            'title' => FILTER_DEFAULT,
            'smallDesc' => FILTER_DEFAULT,
            'filePath' => FILTER_DEFAULT,
            'author'  => FILTER_DEFAULT,
            'stock' => FILTER_VALIDATE_INT,
            'editor'  => FILTER_DEFAULT,
            'loanDuration' => FILTER_VALIDATE_INT,
            'idSubCategorie' => FILTER_VALIDATE_INT,
            'idCollection' => FILTER_VALIDATE_INT,
            'format'  => FILTER_DEFAULT,
            'datePublished'=>FILTER_DEFAULT
        );
        $idArticle["idArticle"] = filter_input(INPUT_POST,'idArticle',FILTER_VALIDATE_INT);
        $data = filter_input_array(INPUT_POST, $args);
        $this->model->update($data,$idArticle);
        echo (json_encode('good'));
    }

    // public function getArticleByUserId(){
    //     if(isset($_SESSION["idUser"])){
    //         $idUser = filter_input(INPUT_GET,'idUser',FILTER_VALIDATE_INT);
    //         $query ="INNER JOIN users ON users.idUser = articles.idUser INNER JOIN categories ON articles.idCategory = categories.idCategorie WHERE articles.idUser = ".$idUser;
    //         echo(json_encode($this->model->getAll($query)));
    //     }else{
    //         echo(json_encode("Not Authorised."));
    //     }
    // }

    // public function updateView(){
    //     $idArticle = filter_input(INPUT_POST,"idArticle",FILTER_VALIDATE_INT);
    //     $id["idArticle"] = $idArticle;
    //     $allData = $this->model->getAll("WHERE idArticle = {$idArticle}");
    //     $data["viewCount"] = $allData[0]["viewCount"] + 1;
    //     echo json_encode($this->model->update($data,$id));
    // }

    public function deleteArticle()
    {
        //il faudrait rajouter une vérification d'identité que l'article appartient réelement à l'utilisateur
        //pour le laisser effacer
        if (isset($_SESSION["idUser"])) {
            $data["idArticle"] = filter_input(INPUT_POST, "idArticle", FILTER_VALIDATE_INT);
            echo json_encode($this->model->delete($data));
        } else {
            echo (json_encode("Not Authorised."));
        }
    }
}
