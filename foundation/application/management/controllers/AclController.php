<?php
	require_once 'BaseController.php';
	require_once 'ORM/Pager/Pager.php';
	
	class Management_Aclcontroller extends BaseController{
		public function indexAction(){
			$gid = HttpUtil::getString("gid");
			if (empty($gid)){
                $this->alert_back("您的操作有误");
			}
			
			$AdmingroupDAO = $this->orm->createDAO("admingroup");
			$AdmingroupDAO ->select('admingroup.*,acl_admin_group.acl_admin_info');
			$AdmingroupDAO ->withAcl_admin_group(array('gid'=>'gid'));
			$AdmingroupDAO ->findGid($gid); 
			$acl_admin_info = $AdmingroupDAO ->get();
			$this->view->assign("acl_admin_info",$acl_admin_info);		
			
			$Acl_infoDAO = $this->orm->createDAO("acl_info");
			$Acl_infoDAO->order('controller,action');
			$aclinfo = $Acl_infoDAO ->get();
			$this->view->assign("aclinfo",$aclinfo);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("acl/index.phtml");
            echo $this->view->render("index/footer.phtml");
		}
		
		public function addaclAction(){
			$gid = HttpUtil::postString("gid");
			if (empty($gid)){
				$this->alert_back("您的操作有误");
			}
			
			$acl = $_POST["acl"];
			if(!empty($acl)){$acl = serialize($acl);}
			$Acl_admin_groupDAO = $this->orm->createDAO("acl_admin_group");
			
			if($this->isacltrue($gid)){
				$Acl_admin_groupDAO ->findGid($gid);
			}else {
				$Acl_admin_groupDAO ->gid = $gid;
			}
			$Acl_admin_groupDAO ->acl_admin_info = $acl;
			$Acl_admin_groupDAO ->save();
			$this->alert_go("权限修改成功", "/admin/ucenter/group?iid=2");
		}
		
		private function isacltrue($gid){
			$Acl_adminDAO = $this->orm->createDAO("acl_admin_group");
			$Acl_adminDAO ->findGid($gid);
			$rs = $Acl_adminDAO ->get();
						
			if(!empty($rs)){
				return TRUE;
			}else {
				return FALSE;
			}
		}

		//权限
		public function acl() {
			if($this->admin_info['gid'] == 15) return;
			parent::acl();
		}
	}