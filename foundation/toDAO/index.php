<?php
	require_once 'PdoTableToDao.php';
	$conn="mysql:host=localhost;port=3306;dbname=my_cms";
	$p=new PdoTableToDao($conn,'root','0okm_nji9','my_cms',''.dirname(__FILE__).'/files/');
	$p->connect();
	$p->output();
?>