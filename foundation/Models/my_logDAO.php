<?php
require_once 'EasyORM.php';
class my_logDAO extends EasyORM{
	 const tableName = 'my_log';
	 const tableField ='logPid,logType,logName,logIp,logTime,logMsg';
	 public $logPid,$logType,$logName,$logIp,$logTime,$logMsg;
	 public function __construct($logPid=null){
		$this->_init(get_defined_vars());
	}
	 public function get($db){
		return $this->selectTable($db);
	}
	 public function save($db){
		return $this->writeTable($db,array());
	}
	 public function del($db){
		return $this->deleteTable($db);
	}
}    			