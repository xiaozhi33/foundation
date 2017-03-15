<?php
class SessionUtil{
	private static $_sessionStart = null;
	public static function sessionStart(){
		if(!self::$_sessionStart){
			self::$_sessionStart = session_start();
		}
		return self::$_sessionStart;
	}
	
	public static function sessionEnd(){
		if(self::$_sessionStart){
			return session_destroy();
		}
	}
	
	public static function sessionGet($name){
		return $_SESSION[$name];
	}
	
	public static function sessionSet($name,$value=''){
		if(is_array($name)){
			foreach ($name as $key=>$item){
				$_SESSION[$key]=$item;
			}
		}
		else $_SESSION[$name]=$value;
	}
	
    public static function checkSession($linkFor=null){
    	self::sessionStart();
        if(empty($_SESSION['userId'])){
        	if(isset($linkFor)) header('location: '.$linkFor);
        	else {
        		$returnURL = base64_encode($_SERVER['REQUEST_URI']);
        		if ($_SERVER['QUERY_STRING']!=''){
        			$returnURL = base64_encode($_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']);
        		}
        		
        		header('location: '.__BASEURL__.'/index/loginview?returnURL='.$returnURL);
        		exit();
        	}
        }
    }
    
    public static function checkadmin($linkFor=null){
    	self::sessionStart();
    	if(in_array($_SESSION['mycmsvip']['admin_info']['admin_type'],array(0,6))){
	    	if(empty($_SESSION['mycmsvip'])){
	        	if(isset($linkFor)) header('location: '.$linkFor);
	        	else {
	        		$returnURL = base64_encode($_SERVER['REQUEST_URI']);
	        		if ($_SERVER['QUERY_STRING']!=''){
	        			$returnURL = base64_encode($_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']);
	        		}
	        		header('location: '.__BASEURL__.'/admin/index/loginview?returnURL='.$returnURL);
	        		exit();
	        	}
	        }
		}else{
			alert_back("您没有进入网站管理后台的权限。请联系超级管理员。");
			//只用系统管理员和网站管理员可以进入网站管理后台
		}
    }
    
	public static function checkmanagement($linkFor=null){
    	self::sessionStart();
    	if($_SESSION['mycmsvip']['admin_info']['admin_type'] == 6){
    		alert_back_old("您没有该模块的管理权限。请联系系统管理员。");
    	}
        if($_SESSION['environment'] != 'test'){
            alert_go_old('非法操作！','/');
        }
        if(empty($_SESSION['mycmsvip'])){
        	if(isset($linkFor)) header('location: '.$linkFor);
        	else {
        		$returnURL = base64_encode($_SERVER['REQUEST_URI']);
        		if ($_SERVER['QUERY_STRING']!=''){
        			$returnURL = base64_encode($_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']);
        		}
        		header('location: '.__BASEURL__.'/management/index/loginview?returnURL='.$returnURL);
        		exit();
        	}
        }
    }

    public static function getUserId(){
    	self::sessionStart();
    	return $_SESSION['userId'];
    }

    public static function getUserType(){
    	return self::sessionGet('userType');
    }
    
    public static function getUserName(){
    	return self::sessionGet('user');
    }
    
    public static function getAdmininfo(){
    	return self::sessionGet('mycmsvip');
    }
    
    public static function initSession($result, $is_management=false){

        if($is_management){
            SessionUtil::sessionSet(array(
                'userType'		=> 'admin',
                'environment'		=> 'management',
                'mycmsvip'		=> array(
                    'admin_id'		=> $result[0]['admin_id'],
                    'admin_name'	=> $result[0]['admin_name'],
                    'admin_info'	=> $result[0]
                )
            ));
        }else {
            SessionUtil::sessionSet(array(
                'userType'		=> 'admin',
                'environment'	=> 'test',
                'mycmsvip'		=> array(
                    'admin_id'		=> $result[0]['admin_id'],
                    'admin_name'	=> $result[0]['admin_name'],
                    'admin_info'	=> $result[0]
                )
            ));
        }
    }

}
?>