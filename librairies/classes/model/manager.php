<?php
namespace Model ;

use PDO;
use PDOException;

abstract class Manager
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

    public function customQuery($query){
        $db = $this->dbConnect();
        $querySql = $db->prepare($query);
            $querySql->execute();
        return $querySql->fetchAll();
    }

    public function getDistinctAll($condition = null, $tableModel=null){
        $db = $this->dbConnect();
        if(is_null($tableModel)){
            $tableModel = $this->table;
        }
        $query = "SELECT DISTINCT ". $condition ." FROM {$tableModel} ";
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
        return $querySql;
    }

    public function update($data,$idArticle,$table=null){
        if(is_null($table)){
            $table = $this->table;
        }
        $db = $this->dbConnect();
        $query = "UPDATE {$table} SET ";
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

    public function updateSet($data,$idArticle,$table=null){
        if(is_null($table)){
            $table = $this->table;
        }
        $db = $this->dbConnect();
        $query = "UPDATE {$table} SET ";
        // $field = array_keys($data);
        // $myArray = array_map(function($key , $value){
            //     return $key . "=" . $value;
        // },$field,array_values($data));
        // $myArray = array_values($data);
        $condition =  array_map(function($key,$value){
            if($value){
                $value1= 1;
            }else{
                $value1=0;
            }
            return $key . "=" .$value1;   },array_keys($data),array_values($data));
        // $query .=  array_map(function($key,$value){
        //     if($value){
        //         $value1= 1;
        //     }else{
        //         $value1=0;
        //     }
        //     return $key . "=" .$value1;   },array_keys($data),array_values($data));
        $query .= implode(",",$condition);
        // $query .= " WHERE " . implode(" AND ",$condition);
        $querySql = $db->prepare($query);
        $querySql->execute();
        return 'Update Done';
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

    public function countAll($idUser=null){
        $db = $this->dbConnect();
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        if($idUser>0){
            $query .= " WHERE idUser=".$idUser;
        }
        $querySql = $db->prepare($query);
        $querySql->execute();
        return $querySql->fetch();
    }
}
