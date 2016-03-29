<?php
    require_once("BaseController.php");
    class Management_searchController extends BaseController
    {
        /**
         * 通用搜索器
         */
        public function indexAction()
        {
            $keywords = $_REQUEST['keywords'];

            $this->view->assign(
              array("keywords" => $keywords)
            );
        }

        public function _init()
        {
            SessionUtil::sessionStart();
            SessionUtil::checkmanagement();
        }
    }