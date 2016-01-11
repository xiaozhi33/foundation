<?php
require_once dirname(__FILE__).'/DAO/DAO.php';
require_once dirname(__FILE__).'/DAO/Join.php';
require_once dirname(__FILE__).'/SQL.php';
require_once dirname(__FILE__).'/Builder/Mysql.php';
require_once dirname(__FILE__).'/Debug/Common.php';
/**
 * 
 * @package ORM
 * @category ORM
 * @example ORM/ORM.php ORM用法介绍
 */
class ORM{
    private static $_config;
    private static $_instanceArray;
    private static $_defaultDNS;
    
    private $_enableDebug = false;
    /**
     * 
     * @var PDO
     */
    private $_pdo;
    
    /**
     * @var ORM_Debug
     */
    private $_debugger;
    
    private function __construct(){}
    
    public static function setConfig($dns ,$user, $password, $charset='utf8'){
        $e = self::$_config[$dns] = get_defined_vars();
        if(!isset(self::$_defaultDNS)) self::$_defaultDNS = $dns;
    }
    
    /**
     * @return ORM
     */
    public static function getInstance($dns = null){
        $dns = $dns?$dns:self::$_defaultDNS;
        if(!isset(self::$_config[$dns])){
            throw new Exception("DNS:{$dns}的ORM尚未配置");
        }
        if(!isset(self::$_instanceArray[$dns])){
            self::$_instanceArray[$dns] = new self();
            self::$_instanceArray[$dns]->_pdo = new PDO(self::$_config[$dns]['dns'],self::$_config[$dns]['user'],self::$_config[$dns]['password']);      
            self::$_instanceArray[$dns]->_pdo->query('set names '.self::$_config[$dns]['charset']);
            self::$_instanceArray[$dns]->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$_instanceArray[$dns]->setDebugger(new ORM_Debug_Common());
        }
        return self::$_instanceArray[$dns];
    }
    
    /**
     * @return ORM_DAO
     * @param $tabelName
     */
    public function createDAO($tabelName){
        $dao = new ORM_DAO($this,$tabelName);
        $dao->setBuilder(new ORM_Builder_Mysql());
        if($this->_enableDebug)$dao->enableDebug();
        return $dao;
    }
    
    /**
     * 
     * @param $tableName
     * @param ORM_DAO $targetDAO
     * @return ORM_DAO_Join
     */
    public function createJoin($tableName,$targetDAO){
    	$join = new ORM_DAO_Join($this, $tableName, $targetDAO);
    	return $join;
    }
    
    public function query($sql, $parms = array()){
        $stmt = $this->prepare($sql);
        $stmt->execute($parms);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function exec($sql, $parms = array()){
        $stmt = $this->prepare($sql);
        $stmt->execute($parms);
        return $this->_pdo->lastInsertId();
    }
    
    public function prepare($sql){
        return $this->_pdo->prepare($sql);
    }
    
    public function beginTran(){
        $this->_pdo->beginTransaction();
    }
    
    public function commit(){
        $this->_pdo->commit();
    }
    
    public function lastInsertId(){
        return $this->_pdo->lastInsertId();
    }
    
    public function rollback(){
        $this->_pdo->rollBack();
    }
    
    public function setDebugger(ORM_Debug_Interface $debugger){
        $this->_debugger = $debugger;
        return $this;
    }
    
    public function getDebugger(){
        return $this->_debugger;
    }
    
    public function enableDebug(){
    	$this->_enableDebug = true;
    	return $this;
    }
    
    public function disableDebug(){
    	$this->_enableDebug = false;
    	return $this;
    }
}
?>