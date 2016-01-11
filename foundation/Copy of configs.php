<?php
	/**
	 *  My new Zend Framework project
     *  @author     zhangwangnan 
 	 *  @version    1.0.1	
     */		
	require_once("Models/DBHelper.php");

	$localhost = 'localhost';
	$rootname = 'root';
	$passwd = '0okm_nji9';
	$dbname = 'my_cms';
	$port = '3306';
	
	define("__HOST__", $localhost);
	define("__PORT__", $port);
	define("__ROOT__", $rootname);
	define("__PASSWD__", $passwd);
	define("__DBNAME__", $dbname);

	//定义后台管理地址
	define("__ADMINHOME__",'http://60.28.60.204:8080/admin');
	
	//定义登陆页面
	define("__VIPMAINLINK__",'http://60.28.60.204:8080/admin/index/loginview');
	
	//定义缓存目录
	@define('__CACHEPATH__','/');
	
	define("__BASEURL__", "http://60.28.60.204:8080");
	define("__ADMININCLUDEPATH__", "/include/admin/");
	define("__DEFAULTINCLUDEPATH__", "/include/default/");
	
	$baseURL = dirname(__FILE__)."/include";
	define("__UPLOADPICPATH__",$baseURL."/upload_file/");
	define("__GETPICPATH__","/include/upload_file/");
	@define("__REPICPATH__", $baseURL.'/upload_file/');
	