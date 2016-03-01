<?php
    ob_start();
    //error_reporting(8);              //notice错误 关闭错误报告
    //error_reporting(E_ALL|E_STRICT);  
    //toDAO
    //require_once 'toDAO/index.php';

    //alert_back("sadfasdfsadfasdf");
    date_default_timezone_set('Asia/Shanghai');
    set_include_path('.' .PATH_SEPARATOR .'../library'.PATH_SEPARATOR .'./application/models/'.PATH_SEPARATOR .'./include/');

    require_once("Zend/Loader.php");
    require_once("../util/functions.php");
    
    function __autoload($class){
		$Module = explode('_',$class);
		if($Module[0] == "Zend"){
			Zend_Loader::loadClass($class);
		}else{
			Zend_Loader::loadFile($class.'.php',"Models/");
		}
	}
	
	require_once("configs.php");

    //设置控制�?
    $controller = Zend_Controller_Front::getInstance();
	$controller->setControllerDirectory(array(
		'default'  =>  'application/default/controllers',
		'admin'    =>  'application/admin/controllers'
	)); 
	
    $controller	->throwExceptions(true);  
    $controller ->setParam('noViewRenderer', true);  
    $controller ->dispatch();        
	
	