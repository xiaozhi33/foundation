<?php
require_once 'BaseController.php';

class Management_CategoryController extends BaseController {
	
	//分类
	public function indexAction() {
		try {
			$cates = $this->orm->createDAO('pm_mg_category')->order('cate_order desc,t_cate_id asc')->get();
			$prepare_cate = array();
			foreach($cates as $cate) {
				$prepare_cate[$cate['t_cate_pid']][] = $cate;
			}
			unset($cates);
			
			$this->view->cates = $prepare_cate;

			echo $this->view->render("index/header.phtml");
			echo $this->view->render('category/category.htm');
			echo $this->view->render("index/footer.phtml");
			exit;
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	//上传分类图片
	public function cateImageUploadAction() {
		try {
			require_once 'util/uploadpic.php';
			require_once 'util/resizepic.php';
			
			if(isset($_FILES['topic_category_image']) && $_FILES['topic_category_image']['error'] != 4){
				$picSavePath = __SITEPATH__ . '/include/upload_file/';
				$uploadpic = new uploadPic($_FILES['topic_category_image']['name'],$_FILES['topic_category_image']['error'],$_FILES['topic_category_image']['size'],$_FILES['topic_category_image']['tmp_name'],'image/jpeg');
				
				$uploadpic->FILE_PATH = $picSavePath;
				$result = $uploadpic->uploadPic();
				//图片尺寸338*338
				if(!empty($result['picname'])){
					$filename = createDst($picSavePath.$result['picname'],338,338);
					
					$this->view->assign(array(
						'origin'		=> $result['picname'],
						'standard'		=> $filename
					));
				}
			}
			
			echo $this->view->render('/management/category/cate-image-upload.htm');
			exit;
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	//添加子分类
	public function addSubcateAction() {
		try {
			$pid = HttpUtil::postString('pid');
			
			$topic_cateDAO = $this->orm->createDAO('pm_mg_category');
			
			if($pid == 0) {
				$path = 0;
			}else {
				$path = $topic_cateDAO->findT_cate_id($pid)->get();
				$topic_cateDAO->unsetFilterField();
				$path = $path[0]['t_cate_path'] . ',' . $pid;
			}
			
			$topic_cateDAO->t_cate_name = HttpUtil::postString('t_cate_name');
			$topic_cateDAO->t_cate_desc = HttpUtil::postString('t_cate_desc');
			$topic_cateDAO->t_cate_image = HttpUtil::postString('image');
			$topic_cateDAO->cate_order = HttpUtil::postString('cate_order');
			$topic_cateDAO->t_cate_path = $path;
			$topic_cateDAO->t_cate_pid = $pid;
			
			$topic_cateDAO->save();
			
			echo '<script type="text/javascript">parent.location.href = "/management/category";</script>';
			exit;
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	//编辑分类
	public function editCateAction() {
		try {
			$cid = HttpUtil::getString('cid');
			
			$topic_cateDAO = $this->orm->createDAO('pm_mg_category')->findT_cate_id($cid);
			$topic_cateDAO->t_cate_name = HttpUtil::postString('t_cate_name');
			$topic_cateDAO->t_cate_desc = HttpUtil::postString('t_cate_desc');
			$topic_cateDAO->t_cate_image = HttpUtil::postString('image');
			$topic_cateDAO->cate_order = HttpUtil::postString('cate_order');
			
			$topic_cateDAO->save();
			
			echo '<script type="text/javascript">parent.location.href = "/management/category";</script>';
			exit;
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	//删除分类
	public function delCategoryAction() {
		try {
			$cid = HttpUtil::getString('cid');
			if(!empty($cid)) {
				$topic_cateDAO = $this->orm->createDAO('pm_mg_category');
				$topic_cateDAO->selectLimit .= ' AND FIND_IN_SET(' . $cid . ',t_cate_path) ';
				$cate_info = $topic_cateDAO->get();
				if(!empty($cate_info)) {
					echo '<script type="text/javascript">alert("请先删除该分类下所有子分类！");</script>';
					exit;
				}
				$topic_cateDAO->selectLimit = '';
				$topic_cateDAO->findT_cate_id($cid)->delete();
				
				echo '<script type="text/javascript">parent.location.href = "/management/category";</script>';
			}else {
				echo '<script type="text/javascript">alert("输入有误！");</script>';	
			}
			
			exit;
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}

	public function _init(){
		$this ->dbhelper = new DBHelper();
		$this ->dbhelper ->connect();
		$this->view->assign("type_arrays", $this->type_arrays);
		SessionUtil::sessionStart();
		SessionUtil::checkmanagement();
	}

	//权限
	public function acl()
	{
		$action = $this->getRequest()->getActionName();
		$except_actions = array(
			'index',
			'add-subcate',
			'edit-cate',
			'del-category',
			'cate-image',
		);
		if (in_array($action, $except_actions)) {
			return;
		}
		parent::acl();
	}
}