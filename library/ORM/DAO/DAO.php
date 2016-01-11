<?php
/**
 * 
 * @example ORM/DAO/DAO.php DAO使用方法
 * @package ORM
 * @category DAO
 *
 */
class ORM_DAO implements ArrayAccess,Countable{
    private $_filterField = array();
    private $_saveField = array();
    private $_joinTable = array();
    private $_tableName;
    private $_tableAlias;
    private $_selectField;
    private $_enabledDebug = false;
    private $_order = '';
    private $_limit = '';
    /**
     * 
     * @var ORM_Builder
     */
    private $_builder;
    
    private $_cacheData = array();
    
    /**
     * 
     * @var ORM
     */
    private $_ORM;
    
    private $_debugger;
    
    public $selectLimit = null;
    
    public function __construct(ORM $ORM,$tableName){
        $this->_ORM = $ORM;
        $this->_tableName = $this->_tableAlias = $tableName;
    }
    
    public function get($q=''){
        $sql = $this->_builder->selectSql($this);
        $key = bin2hex($sql);
        if(!isset($this->_cacheData[$key])){
        	$this->_prepareSql($sql);
            $this->_cacheData[$key] = $this->_ORM->query($sql);
        }
        return $this->_cacheData[$key];
    }
    
    public function select($selectFields){
        $this->_selectField = $selectFields;
        return $this;
    }
    
    public function save(){
        if(empty($this->_filterField)){    
            $this->_ORM->exec($this->_prepareSql($this->_builder->insertSql($this)));
            return $this->_ORM->lastInsertId();
        }else{
            $this->_ORM->exec($this->_prepareSql($this->_builder->updateSql($this)));
        }
    }
    
    public function delete(){
        if(!empty($this->_filterField)){
            $this->_ORM->exec($this->_prepareSql($this->_builder->deleteSql($this)));
        }
    }
    
    private function _prepareSql($sql){
        if($this->_enabledDebug){
            $this->getDebugger()->debug($sql);
        }
        return $sql;
    }
    
    public function alias($alias){
        $this->_tableAlias = $alias;
        return $this;
    }
    
    /**
     * method: order
     * param: (string or array) return $string
     * 
     */
    
    public function order($orders)
    {
        $sql = '';
        if (is_array($orders)) {
            foreach ($orders as $order) {
                $sql .= ', ' . $order[0] . ' ' . (isset($order[1]) ? $order[1] : 'ASC');
            }
        } else {
            $sql .= $orders;
        }
        ltrim($sql, ', ');
        $this->_order = ' order by '.ltrim($sql, ', ');
        return $this;
    }
    
    public function limit($limit, $counts = null)
    {
        if(!empty($limit) || $limit=='0'){
            $this->_limit = '';
            $this->_limit .= ' limit ' . $limit;
            if (isset($counts)) {
                $this->_limit .= ', ' . $counts;
            }
        }else{
            $this->_limit = '';
        }
        return $this;
    }
    
    public function getOrder()
    {
        return $this->_order;
    }
    
    public function getLimit()
    {
        return $this->_limit;
    }
    
    /**
     * @return ORM_Builder
     */
    public function getBuilder(){
        return $this->_builder;
    }
    
    public function setBuilder(ORM_Builder_Interface $builder){
        $this->_builder = $builder;
    }
    
    public function setDebugger(ORM_Debug_Interface $debugger){
        $this->_debugger = $debugger;
    }
    
    public function getDebugger(){
        return isset($this->_debugger)?$this->_debugger:$this->_ORM->getDebugger();
    }
    
    public function enableDebug(){
        $this->_enabledDebug = true;
        return $this;
    }
    
    public function disableDebug(){
        $this->_enabledDebug = false;
        return $this;
    }
    
    public function getFilterField(){
        return $this->_filterField;
    }
    
    public function unsetFilterField(){
        $this->_filterField = array();
    }
    
    public function getJoinTable(){
        return $this->_joinTable;
    }
    
    public function getSaveField(){
        return $this->_saveField;
    }
    
    public function getSelectField(){
        return $this->_selectField;
    }
    
    public function getAlias(){
        return $this->_tableAlias;
    }
    
    public function getORM(){
        return $this->_ORM;
    }
    
    /**
     * @return ORM_Pager
     * @param $option
     */
    public function getPager($option = array()){
        if(!class_exists('ORM_Pager',false)){
            require_once 'ORM/Pager/Pager.php';
        }
        return new ORM_Pager($this,$option);
    }
    
    /**
     * @return ORM_Form
     * @param $options
     * @param $translator
     */
    public function getForm($options=null,Zend_Translate $translator = null){
        if(!class_exists('ORM_Form',false)){
            require_once 'ORM/Form/Form.php';
        }
        return ORM_Form::getORMFormInstance($this,$options,$translator);
    }
    
    public function offsetExists($offset){
        return isset($this->_saveField[$offset]);
    }
    
    public function offsetGet($offset){
        return $this->_saveField[$offset];
    }
    
    public function offsetSet($offset, $value){
        $this->_saveField[$offset] = $value;
    }
    
    public function offsetUnset($offset){
        unset($this->_saveField[$offset]);
    }
    
    public function count(){
    	return count($this->_saveField);
    }
    
    public function __call($fun,$args){
        if(substr($fun,0,4) === 'find'){
            $filter = array();
            $filter['field'] = substr($fun,4);
            $filter['field'][0] = strtolower($filter['field'][0]);
            $filter['value'] = $args[0];
            $filter['condition'] = isset($args[1])?$args[1]:'=';
            if(isset($args[0])){
            	$this->_filterField[] = $filter;
            }
            return $this;
        }elseif (substr($fun,0,4) === 'with'){
            $table = array();
            $filed = substr($fun,4);
            $filed[0] = strtolower($filed[0]);
            $joinDAO = $this->_ORM->createJoin($filed,$this);
            $joinDAO->setJoinMap($args[0]);
            isset($args[1])&&$joinDAO->setJoinType($args[1]);
            $this->_joinTable[] = $joinDAO;
            return $joinDAO;
        }
    }
    
    public function __get($name){
        $tmpData = $this->get();
        return $tmpData[0][$name];
    }
    
    public function __set($name,$value){
        $this->_saveField[$name] = $value;
    }
    
    public function __toString(){
        return $this->_tableName;
    }
}
?>