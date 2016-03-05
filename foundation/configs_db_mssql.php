<?php
	/**
	 *  My new Zend Framework project
     *  @author     zhangwangnan 
 	 *  @version    1.0.1	
     */		

	$localhost = '127.0.0.1';
	$rootname = 'root';
	$passwd = '';
	$dbname = 'test1';
	$port = '3306';
	
	define("__HOST__", $localhost);
	define("__PORT__", $port);
	define("__ROOT__", $rootname);
	define("__PASSWD__", $passwd);
	define("__DBNAME__", $dbname);

    $conn = mssql_connect(__HOST__,__ROOT__,__PASSWD__) or die ("connect failed");
    mssql_select_db(__DBNAME__, $conn);
    //mssql_query('SET NAMES \'UTF8\'');

    //执行查询语句
    $query = "select * from users";
    $row = mssql_query($query);

    //打印输出查询结果=
    while($list=mssql_fetch_array($row))
    {
        print_r($list);
        echo "<br>";
    }
	