<?php
	require_once("Zend/Mail.php");
	require_once("Zend/Mail/Transport/Smtp.php");
	
	class sendmail{
		/**
		 * Enter description here... 邮件发送类 
		 *
		 * @param $type  		邮件类型默认html 可选为test
		 * @param $mailbody  	邮件内容
		 * @param $smtp  		SMTP邮件服务器
		 * @param $username  	用户名
		 * @param $password     密码
		 * @param $subject      邮件标题
		 * @param $form         邮件发送来源
		 * @param $to           邮件发送到。。。
		 */
		public function sendtomailAction($type="html",$mailbody=null,$subject,$to="61094462@qq.com",$smtp="smtp.163.com",$username="ceshi1du",$password="060700",$form="ceshi1du@163.com"){
			try {
				$config=array('auth'=>'login','username'=>$username,'password'=>$password);
				$transport = new Zend_Mail_Transport_Smtp($smtp,$config);
				
				if(is_array($to)){
					foreach ($to as $key => $value){
						$mail = new Zend_Mail();
						$html = $mailbody;
						$mail ->setBodyHtml($html,'utf-8');
						$mail ->setFrom($form,"=?UTF-8?B?".base64_encode('woow100')."?=");
						$mail ->addTo($value,"=?UTF-8?B?".base64_encode('尊敬的woow100用户')."?=");
						
						$mail ->setSubject("=?UTF-8?B?".base64_encode($subject)."?="); //主题
						$mail ->addHeader('X-MailGenerator','MyCoolApplication');
						$mail ->send($transport);
					}
				}else {
						$mail = new Zend_Mail();
						$html = $mailbody;
						$mail ->setBodyHtml($html,'utf-8');
						$mail ->setFrom($form,"=?UTF-8?B?".base64_encode('woow100')."?=");
						$mail ->addTo($to,"=?UTF-8?B?".base64_encode('尊敬的woow100用户')."?=");
						
						$mail ->setSubject("=?UTF-8?B?".base64_encode($subject)."?="); //主题
						$mail ->addHeader('X-MailGenerator','MyCoolApplication');
						$mail ->send($transport);
				}
				
			}catch (Exception $e){
				throw $e;
			}
		}
	}	
?>
