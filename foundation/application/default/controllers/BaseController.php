<?php
	require_once("../util/sessionutil.php");
	require_once("../util/httputil.php");
	require_once("../util/functions.php");

	class BaseController extends Zend_Controller_Action
	{
		public function init()
	    {    	
	    	$this->view = new Zend_View();
			//$this->view ->addScriptPath('application/default/views/scripts');
			
			$this->view ->setScriptPath('application/default/views/scripts');
			//$controller = $this->getRequest()->getControllerName();
			//$action = $this->getRequest()->getActionName();
			//$this->view->assign('c',$controller);
		    $this->_init();
	    }
	    
	    public function _init(){
	    	
	    }
	}
