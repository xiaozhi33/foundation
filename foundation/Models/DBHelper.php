<?php
class DBHelper{
    private $db;
    public function connect($DBNAME=''){
        if(empty($DBNAME)){
            $conn = "mysql:host=". __HOST__ .";port=".__PORT__.";dbname=" . __DBNAME__;
        }else {
            $conn = "mysql:host=". __HOST__ .";port=".__PORT__.";dbname=" . $DBNAME;
        }

        $username = __ROOT__;
        $password = __PASSWD__;
        try{
            $this->db = new PDO("{$conn}","{$username}","{$password}");
            $this->db->query("SET NAMES 'utf8'");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (Exception $e){
            throw new Exception("Connect failed!".$e->getMessage());
        }

    }
    public function factory(){
    	$db=new DBHelper();
    	$db->connect();
    	return $db;
    }
    public function fetchAllData($sql,$parArray = array()){
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute($parArray);
            //$stmt->setFetchMode(PDO::FETCH_COLUMN);
            $resArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resArray;
        }catch (Exception $e){
            echo $sql;
            throw $e;
        }
    }
    public function getAllData($sql,$parArray = array()){
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute($parArray);
            $resArray = $stmt->fetchAll();
            return $resArray;
        }catch (Exception $e){
            echo $sql;
            throw $e;
        }
    }
    public function executeOne($sql,$parArray = array()){
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute($parArray);
            return $this->db->lastInsertId();
        }catch (Exception $e){
            echo $sql;
            throw $e;
        }
    }
    public function beginTran(){
        try{
            $this->db->beginTransaction();
        }catch (Exception $e){
            throw $e;
        }

    }
    public function commit(){
        try{
            $this->db->commit();
        }catch (Exception $e){
            throw $e;
        }
    }
    public function rollback(){
        try{
            $this->db->rollBack();
        }catch (Exception $e){
            throw $e;
        }
    }
}
?>