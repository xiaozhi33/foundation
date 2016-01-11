<?php
class DBHelper{
    private $db;
    private static $_instances = null;
    public function connect(){
        $conn = "mysql:host=". __HOST__ .";port=".__PORT__.";dbname=" . __DBNAME__;
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
    /**
     * @return DBHelper
     */
    public static function factory(){
        if(!isset(self::$_instances['default'])){
            self::$_instances['default']=new DBHelper();
            self::$_instances['default']->connect();
        }
    	return self::$_instances['default'];
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