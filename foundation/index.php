<?php
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('PRC');
	ob_start();
	error_reporting(0);              //notice错误 关闭错误报告
    //error_reporting(E_ALL|E_STRICT);
    set_include_path('.' .PATH_SEPARATOR .'../library'.PATH_SEPARATOR .'./application/models/'.PATH_SEPARATOR .'./include/'.PATH_SEPARATOR .'./Models/'.PATH_SEPARATOR .'../public/'.PATH_SEPARATOR .'../util/');

    require_once("Zend/Loader.php");
    require_once("functions.php");
	
    function __autoload($class){
		$Module = explode('_',$class);
		if($Module[0] == "Zend"){
			Zend_Loader::loadClass($class);
		}else{
			Zend_Loader::loadFile($class.'.php',"Models/");
		}
	}
	
	require_once("configs.php");
	
    $controller = Zend_Controller_Front::getInstance();
	$controller->setControllerDirectory(array(
		'default'  =>  'application/default/controllers',
		'admin'    =>  'application/admin/controllers',      //网站后台管理
		'management'	=>	'application/management/controllers'	//项目管理系统
	)); 
	
    $controller	->throwExceptions(true);  
    $controller ->setParam('noViewRenderer', true);
    $controller ->dispatch();        
	
	