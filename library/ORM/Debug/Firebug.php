<?php
/**
 * @package ORM
 */
require_once 'Interface.php';
require_once 'Zend/Log/Writer/Firebug.php';

class ORM_Debug_Firebug implements ORM_Debug_Interface{
	private $_writer;
	private $_logger;
	
	public function __construct(){
		$this->_writer = new Zend_Log_Writer_Firebug();
		$this->_logger = new Zend_Log($this->_writer);
	}
	
    public function debug($sql){
        $this->_logger->log('ORM: '.$sql, Zend_Log::INFO);
        ob_flush();
    }
}