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
	public static function sessionSet($name,$value=''){
		if(is_array($name)){
			foreach ($name as $key=>$item){
				$_SESSION[$key]=$item;
			}
		}
		else $_SESSION[$name]=$value;
	}
	public static function initSession($result){
		$admin = array(
				'uid'			=> $result['uid'],
				'picname'		=> $result['picname'],
				'username'		=> $result['username'],
				'gid'			=> $result['gid'],
				'gname'			=> $result['gname'],
				'rank'			=> $result['rank']
			);
		SessionUtil::sessionSet(array(
			'admin'			=> $admin
		));
		if ($date){
			SessionUtil::setCookiesForSession($user,$date);
		}
	}
	public static function sessionGet($name){
		return $_SESSION[$name];
	}
	public static function sessionGetByKey($name){
		return $_SESSION['users'][$name];
	}
	public static function setCookiesForSession($result,$date){
		if ($date == '1' || $date == '7' || $date == '30'){
			foreach ($result as $k => $val){
				$str .= $k . ':' . $val . ',';
			}
			$str = mb_substr($str,0,-1);
			$time = $date * 86400;
			$str = Security::encrypt($str,'log&=in',$time);
			setcookie('yidu_hr_user',$str,time()+$time,'/');
		}
	}
	public function checkCookiesForSession(){
		if ($_COOKIE['yidu_hr_user']){
			$str = Security::decrypt($_COOKIE['yidu_hr_user'],'log&=in');
			$str = explode(',',$str);
			foreach ($str as $val){
				$val = explode(':',$val);
				$user[$val['0']] = $val['1'];
			}
			SessionUtil::initSession($user);
		}
	}
	public static function checkLogin(){
		self::sessionStart();
		if(!self::sessionGet('users')){
			echo header('Content-type:text/html;charset=utf-8');
			echo '<script type="text/javascript">alert(\'请先注册或者登陆\');</script>';
			echo '<script type="text/javascript">location.href="/login/company";</script>';
			exit;		
		}
	}
}
?>