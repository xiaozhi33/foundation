<?php
class CookieUtil{
	public static function cookieEnd($name,$value=''){
		setcookie($name,$value,time()-86400 * 100000,'/');
	}
	public static function cookieSet($name,$value='',$timeinfo=0){
		setcookie($name,$value,$timeinfo,'/');
	}
	
	public static function cookieGet($name){
		if(isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		}else {
			return null;
		}
	}
	
	/*
	 * 语言包设置
	 * */
	public static function langinitCookie($result){
		//$result = "zh_CN";
		$lang = array(
				'lang'			=> $result
			);
		$lang = serialize($lang);
		self::cookieSet("chinadaily_lang",$lang);
	}

	/*
	 * 前台用户验证
	 * $result 登陆后用户信息
	 * */
	public static function userinitCookie($result){
		$user = array(
				'uid'			=> $result[0],
				'username'		=> $result[1],
				'email'			=> $result[2]
			);
		//admin数组序列化
		$user = serialize($user);
		self::cookieSet("chinadaily_auth",$user);
	}
	public static function usercheckLogin($mes='请先注册或者登陆'){
		if(!self::cookieGet('xhneng_auth')){
			echo header('Content-type:text/html;charset=utf-8');
			echo '<script type="text/javascript">alert(\''.$mes.'\');</script>';
			echo '<script type="text/javascript">location.href="/login";</script>';
			exit;		
		}
	}
	
	/*
	 * admin后台用户验证
	 * $result 登陆后用户信息
	 * */
	public static function initCookie($result){
		$admin = array(
				'uid'			=> $result['uid'],
				'picname'		=> $result['picname'],
				'username'		=> $result['username'],
				'gid'			=> $result['gid'],
				'gname'			=> $result['gname'],
				'rank'			=> $result['rank'],
				'acl_group'		=> $result['acl_admin_info']
			);
		//admin数组序列化
		$admin = serialize($admin);
		self::cookieSet("fasttopic_auth",$admin);
	}
	public static function checkLogin(){
		if(!self::cookieGet('fasttopic_auth')){
			echo header('Content-type:text/html;charset=utf-8');
			echo '<script type="text/javascript">alert(\'请先注册或者登陆\');</script>';
			echo '<script type="text/javascript">location.href="/login/company";</script>';
			exit;		
		}
	}
}
?>