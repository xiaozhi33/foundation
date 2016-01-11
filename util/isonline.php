<?php 
	/**
	 * verson 1.01
	 * author zhangwangnan
	 */
	class isonline{
		//判断是否过期
		public $online_times = 3600; //在线的时间差秒数，这里设置为1小时
		
		public function isNotIp($ip){    //判断IP是否存在
			try {
				if(!$ip){
					return true;
				}else{
					return false;
				}
			}catch (Exception $e){
				throw $e;
			}
		}
		 //是否在线
		public static function is_online($user_id,$db){		
			$sessionDAO = new woow_user_sessionDAO();
			$sessionDAO->user_id = $user_id;
			$session = $sessionDAO->get($db);
			if(!empty($session)){
				return true;
			}else{
				return false;
			}
		}

		//谁看过我
		public static function seeMe($user_id,$db){			
			$viewlogDAO = new woow_viewlogDAO();
			$viewlogDAO->visited_id = $user_id;
			$viewlogDAO->selectField("woow_viewlog.*,woow_user.user_id,woow_user.user_nickname,woow_user.user_sex,woow_user_info.user_headpic,woow_user.user_sex");
			$viewlogDAO->joinTable("inner join woow_user on woow_user.user_id = woow_viewlog.visitor_id");
			$viewlogDAO->joinTable("inner join woow_user_info on woow_user_info.user_id = woow_viewlog.visitor_id");
			$viewlogDAO->selectLimit = "order by woow_viewlog.time desc limit 0,12";
			$seeMe = $viewlogDAO->get($db);
			return $seeMe;		
			
		}
		
		//我看过谁
		public static function meSee($user_id,$db){			
			$viewlogDAO = new woow_viewlogDAO();
			$viewlogDAO->visitor_id = $user_id;
			$viewlogDAO->selectField("woow_viewlog.*,woow_user.user_id,woow_user.user_nickname,woow_user.user_sex,woow_user_info.user_headpic,woow_user.user_sex");
			$viewlogDAO->joinTable("inner join woow_user on woow_user.user_id = woow_viewlog.visited_id");
			$viewlogDAO->joinTable("inner join woow_user_info on woow_user_info.user_id = woow_viewlog.visited_id");
			$viewlogDAO->selectLimit = "order by woow_viewlog.time desc limit 0,12";
			$seeMe = $viewlogDAO->get($db);
			return $seeMe;		
			
		}
	}
?>