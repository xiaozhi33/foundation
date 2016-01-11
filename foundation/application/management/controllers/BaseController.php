<?php
	require_once("sessionutil.php");
	require_once("httputil.php");
	require_once("functions.php");
	require_once 'resizepic.php'; //创建缩略图
	require_once 'uploadpic.php';
	
	$uploadpicpath = __UPLOADPICPATH__;//上传图片路径

	class BaseController extends Zend_Controller_Action
	{
		public function init()
	    {
	    	$request_mod = $this->getRequest()->getParams();
			$this->view = new Zend_View();
			$this->view ->addScriptPath('application/management/views/scripts');
			
			$this->view->assign(array(
				"module" => $request_mod['module'],
				"controller" => $request_mod['controller'],
				"action" => $request_mod['action'],
			));
			
		    $this->_init();
	    }
	    
	    public function _init(){
	 		
	    }
	}