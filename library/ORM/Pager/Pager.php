<?php
/**
 * @package ORM
 * @subpackage Pager
 */
require_once 'Pager/Pager.php';
class ORM_Pager{
    /**
     * @var ORM_DAO
     */
    private $_dao;
    
    /**
     * 
     * @var Array
     */
    private $_options;
    
    /**
     * 
     * @param EasyORM $ORM
     */
    public function __construct(ORM_DAO $dao,$options=null){
        $this->_dao = $dao;
        $this->setOptions($options);
    }
    
    /**
     * 
     * @param Array $options
     * @return ORMPager
     * 
     */
    public function setOptions($options){
        $parms = array(
              'perPage'                   => 20,
              'delta'                     => 8,
              'mode'                      => 'sliding',
              'httpMethod'                => 'GET',
              'urlVar'                    => 'pageID',
              'altPrev'                   => '上一页',
              'altNext'                   => '下一页',
              'nextImg'                   => '下一页',
              'prevImg'                   => '上一页',
              'separator'                 => '',
              'spacesBeforeSeparator'     => 0,
              'spacesAfterSeparator'      => 1
        );
        if(is_array($options))
        foreach ($options as $key=> $value) {
            $parms[$key] = $value;
        }
        
        $this->_options = $parms;
        return $this;
    }
    
    /**
     * 返回当前参数数组
     * 
     */
    public function getOptions(){
        return $this->_options;
    }
    
    public function returnArray(){
        $options = $this->getOptions();
        $options['itemData'] = null;
        
        $pageID = isset($_REQUEST[$options['urlVar']])?(int)$_REQUEST[$options['urlVar']]:1;
        $pageID = ($pageID-1)>0?$pageID-1:0;
        $pageSize = $options['perPage'];
        
        $this->_dao->limit($pageID*$pageSize,$pageSize);
        $selectFeild = $this->_dao->getSelectField();
        $selectFeild = is_string($selectFeild)?$selectFeild:'*';
        $pageData = $this->_dao->select('SQL_CALC_FOUND_ROWS '.$selectFeild)->get();
        if(!isset($options['totalItems'])){
            $total = $this->_dao->getORM()->query('select FOUND_ROWS() as total');
            $options['totalItems'] = $total[0]['total'];
        }
        
        $pager = Pager::factory($options);
        
        $links = $pager->getLinks();
        $links['all'] = str_replace('index.php','',$links['all']);
        
        return array(
              'pageData'        => $pageData,
              'page'            => $links,
              'totalItems'      => $options['totalItems']
        );
        
    }
    
    /**
     * @return ORM_Pager
     * @param $view
     */
    public function assignTo(Zend_View_Abstract $view){
        $view->assign($this->returnArray());
        return $this;
    }
}