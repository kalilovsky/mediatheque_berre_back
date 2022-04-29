<?php

namespace Controller;

class LoansController extends Controller
{
    protected $modelName = "LoansModel";

    public function index()
    {
        return ("Api introuvable, veuillez réessayer svp.");
    }

    public function getAllCategories()
    {
        echo (json_encode($this->model->getAll("", "categories")));
    }

    public function addLoans()
    {
        $args = array(
            'idUser' => FILTER_VALIDATE_INT,
            'idArticle' => FILTER_VALIDATE_INT,
        );
        $data = filter_input_array(INPUT_POST,$args);
        if($this->model->insert($data)){
            $articleLoaned = $this->model->getAll("WHERE idArticle = ".$data['idArticle'],'articles');
            $idArticle["idArticle"]=$data['idArticle'];
            if($articleLoaned[0]['stock']>0){
                $data2['stock'] = $articleLoaned[0]["stock"] - 1;
                $updateArticle = $this->model->update($data2,$idArticle,'articles');
                $updateArticle["message"] = "Article emprunté correctement";
                echo(json_encode($updateArticle));
            }else{
                $updateArticle["message"] = "Stock de l'article insuffisant!";
                echo(json_encode($updateArticle));
            }
        }else{
            echo (json_encode("Problème lors de l'emprunt."));
        }
    }

    public function getAllLoansByUser(){
        $idUser = filter_input(INPUT_POST,"idUser",FILTER_VALIDATE_INT);
        echo(json_encode($this->model->getAll("INNER JOIN (SELECT articles.*, subcategories.SubcategorieName,categories.categorieName FROM articles INNER JOIN categories ON  categories.idCategorie = articles.idCategory INNER JOIN subcategories ON subcategories.idSubCategorie = articles.idSubCategorie ) AS a ON loans.idArticle = a.idArticle WHERE loans.idUser = ".$idUser)));
    }
    public function getAllLoans(){
        $idUser = filter_input(INPUT_POST,"idUser",FILTER_VALIDATE_INT);
        echo(json_encode($this->model->customQuery("SELECT * ,DATEDIFF(CURRENT_TIMESTAMP,loansDate) as duration FROM loans INNER JOIN (SELECT articles.*, subcategories.SubcategorieName,categories.categorieName FROM articles INNER JOIN categories ON  categories.idCategorie = articles.idCategory INNER JOIN subcategories ON subcategories.idSubCategorie = articles.idSubCategorie ) AS a ON loans.idArticle = a.idArticle INNER JOIN users ON users.idUser = loans.idUser")));
    }
    public function getAllPendingLoans(){
        $idUser = filter_input(INPUT_POST,"idUser",FILTER_VALIDATE_INT);
        $query = "SELECT * ,DATEDIFF(CURRENT_TIMESTAMP,loansDate) as duration FROM loans INNER JOIN (SELECT articles.*, subcategories.SubcategorieName,categories.categorieName FROM articles INNER JOIN categories ON  categories.idCategorie = articles.idCategory INNER JOIN subcategories ON subcategories.idSubCategorie = articles.idSubCategorie ) AS a ON loans.idArticle = a.idArticle INNER JOIN users ON users.idUser = loans.idUser WHERE loans.status ='encours'";
        if ($idUser>0){
            $query .= " AND loans.idUser =".$idUser;
        }
        echo(json_encode($this->model->customQuery($query)));
    }
    public function getAllFinishedLoans(){
        $idUser = filter_input(INPUT_POST,"idUser",FILTER_VALIDATE_INT);
        $query = "SELECT * ,DATEDIFF(CURRENT_TIMESTAMP,loansDate) as duration FROM loans INNER JOIN (SELECT articles.*, subcategories.SubcategorieName,categories.categorieName FROM articles INNER JOIN categories ON  categories.idCategorie = articles.idCategory INNER JOIN subcategories ON subcategories.idSubCategorie = articles.idSubCategorie ) AS a ON loans.idArticle = a.idArticle INNER JOIN users ON users.idUser = loans.idUser WHERE loans.status ='rendu'";
        if ($idUser>0){
            $query .= " AND loans.idUser =".$idUser;
        }
        echo(json_encode($this->model->customQuery($query)));
    }

    public function getLateLoans(){
        $idUser = filter_input(INPUT_POST,"idUser",FILTER_VALIDATE_INT);
        $query = "SELECT * ,DATEDIFF(CURRENT_TIMESTAMP,loansDate) as duration FROM loans INNER JOIN (SELECT articles.*, subcategories.SubcategorieName,categories.categorieName FROM articles INNER JOIN categories ON  categories.idCategorie = articles.idCategory INNER JOIN subcategories ON subcategories.idSubCategorie = articles.idSubCategorie ) AS a ON loans.idArticle = a.idArticle INNER JOIN users ON users.idUser = loans.idUser WHERE DATEDIFF(CURRENT_TIMESTAMP,loans.loansDate) >a.loanDuration AND loans.status = 'encours'";
        if ($idUser>0){
            $query .= " AND loans.idUser =".$idUser;
        }
        echo(json_encode($this->model->customQuery($query)));
    }
    
    public function getCountLoans(){
        $idUser = filter_input(INPUT_POST,"idUser",FILTER_VALIDATE_INT);
        echo(json_encode($this->model->countAll($idUser)));
    }

    public function checkLateLoansByUser(){

    }

    public function returnLoan(){
        $args = array(
            'idLoan'=> FILTER_VALIDATE_INT,
            'idUser' => FILTER_VALIDATE_INT,
            'idArticle' => FILTER_VALIDATE_INT,
        );
        $data = filter_input_array(INPUT_POST,$args);
        if (count($this->model->getAll("WHERE idLoan=".$data['idLoan']." AND idArticle=".$data['idArticle']))===1){
            $article = $this->model->getAll("WHERE idArticle=".$data['idArticle'],"articles");
            $data1["stock"]=$article[0]["stock"] + 1;
            $idArticle["idArticle"] = $data['idArticle'];
            $idLoan["idLoan"]=$data['idLoan'];
            $data2["status"]="rendu";
            $data2["updateDateLoan"]=date('Y-m-d H:i:s');
            $updateArticle = $this->model->update($data1,$idArticle,'articles');
            $updateLoan = $this->model->update($data2,$idLoan);
            if(!is_null($updateArticle) && !is_null($updateLoan)){
                $updateLoan["message"]="Article rendu";
                echo json_encode($updateLoan);
            }
        }
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
