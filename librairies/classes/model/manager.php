<?php
namespace Model ;

use PDO;
use PDOException;

class Manager
{
    protected function dbConnect()
    {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];        
        try {
            $db = new PDO('mysql:host=localhost;dbname=mkMediaBdd;charset=utf8', 'khalil', 'root',$options);
            // $db = new PDO('mysql:host=i54jns50s3z6gbjt.chr7pe7iynqr.eu-west-1.rds.amazonaws.com;dbname=ye7bixnka5y0ro2o;charset=utf8', 'xoa19fqr9q160r20', 'pop08ividy63k71j',$options);
            return $db;
        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    public function getAll($condition = null, $tableModel=null){
        $db = $this->dbConnect();
        if(is_null($tableModel)){
            $tableModel = $this->table;
        }
        $query = "SELECT * FROM {$tableModel} " . $condition;
        $querySql = $db->prepare($query);
        $querySql->execute();
        return $querySql->fetchAll();
    }

    public function insert($data){
        $db = $this->dbConnect();
        $query = "INSERT INTO {$this->table} (";
        $field = array_keys($data);
        $query .= implode(",",$field) . ") VALUES (";
        $params = array_map(function($field){
            return ":$field";
        },$field);
        $query .= implode(", ", $params) . ")";
        $querySql = $db->prepare($query);
        $querySql->execute($data);
    }

    public function update($data,$idArticle){
        $db = $this->dbConnect();
        $query = "UPDATE {$this->table} SET ";
        $field = array_keys($data);
        $query .= implode("= ? ,",$field)." = ?";
        // $myArray = array_map(function($key , $value){
        //     return $key . "=" . $value;
        // },$field,array_values($data));
        $myArray = array_values($data);
        $condition =  array_map(function($key,$value){
            return $key . "=" .$value;   },array_keys($idArticle),array_values($idArticle));
        $query .= " WHERE " . implode(" AND ",$condition);
        $querySql = $db->prepare($query);
        $querySql->execute($myArray);
        return $idArticle;
    }

    public function delete($data){
        $db = $this->dbConnect();
        $query = "DELETE FROM {$this->table} WHERE ";
        $field = array_keys($data);
        $query .= $field[0] . "=" . $data[$field[0]]; 
        $querySql = $db->prepare($query);
        $querySql->execute();
        if ($querySql) {
            return 'delete done';
        }
        return "problem detected";
    }
}
