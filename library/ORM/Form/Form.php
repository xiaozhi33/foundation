<?php
/**
 * @package ORM
 * @subpackage Form
 */
require_once 'Zend/Form.php';
require_once 'Zend/Translate.php';
class ORM_Form extends Zend_Form{
    private $ORMList;
    private $_ormIndex = 0;
    private $_elementOrder = 0; 
    private $_useMySort=false;
    private $_cacheAble = false;
    private $_autoSubmit = true; //是否自动添加提交按钮
    private $_cache = null;
    
    private $_eventList = array();
    
    const DUPLICATE_SUBMIT = 'duplicate_submit';
    
    /**
     * 将表单与ORM绑定
     * @param EasyORM $ORM  自动完成表单name到ORM属性的映射
     * @param Array $keyMap 设定表单name到ORM属性的映射，该映射将覆盖自动映射中同名的项
     * @param Array|String $whitelist 白名单绑定，一旦指定，将完全忽略自动映射和指定的映射
     * @param Array $formData ORM的初始值，用于优化更新操作
     */
    public function bindORM(ORM_DAO $ORM,$keyMap=array(),$whitelist=array(),$formData=null){
        if(is_string($whitelist)){
            $temp = explode(',',$whitelist);
            $whitelist = array();
            foreach ($temp as $name){
                $whitelist[$name] = $name;
            }
        }
        $this->ORMList[$this->_ormIndex]['orm'] = $ORM;
        $this->ORMList[$this->_ormIndex]['keymap'] = $keyMap;
        $this->ORMList[$this->_ormIndex]['whitelist'] = $whitelist;
        $this->ORMList[$this->_ormIndex]['data'] = $formData;
        $this->_ormIndex++;
        return $this;
    }
    
    /**
     *  将Form的数据保存到数据库
     * @param int index 仅执行ORMList[index]的save操作
     */
    public function save($index=null){
        $this->handleEvent('saveBefore');
        
        if(is_array($this->ORMList))
        foreach ($this->ORMList as $pos=>$item){
            if(isset($index)&& $pos!==$index) continue;
            if(is_array($item['whitelist'])&&!empty($item['whitelist'])){ //如果指定白名单则仅读取白名单
                foreach ($item['whitelist'] as $name=>$property){
                    if(!isset($item['orm'][$property])&& $element = $this->getElement($name))
                    $item['orm'][$property] = $element->getValue();
                }
            }else{ //ORM到表单映射
                foreach ($this->getElements() as $element){
                    $name = $element->getName();
                    if(isset($item['keymap'][$name])){ //如果keyMap中有映射关系则采用keymap的设定
                        if(!isset($item['orm'][$item['keymap'][$name]]))
                        $item['orm'][$item['keymap'][$name]] = $element->getValue();
                    }
                }
            }
            try {
                //优化更新操作，只更新变化的字段
                if(isset($item['data'])&&is_array($item['data'])){
                    foreach ($item['data'] as $name => $value) {
                        if($item['orm'][$name] === $value){
                            unset($item['orm'][$name]);
                        }
                    }
                }
                if(count($item['orm']) > 0){ //有更新
                	$lastId[$pos] = $item['orm']->save();	
                }
            }catch (Exception $e){
                echo $e->getMessage();
            }
            
        }
        
    	$this->handleEvent('saveAfter');
        
        return $lastId;
    }
    
    public function autoSave($redirectURL = null){
        if($_POST&&$this->isValid($_POST)){
            $lastID = $this->save();
            if(isset($redirectURL)){
                Zend_Controller_Front::getInstance()->getResponse()->setRedirect(
                	$redirectURL===true?$this->getReferer():$redirectURL
                );
            }
            return $lastID;
        }
    }
    
    /**
     * 获取ORMForm实例
     * @param EasyORM $ORM
     * @param Array $options
     * $options = array(
     *      'property'      => array(
     *              'element'       => 'text',
     *              'options'       => array()
     *      )
     * )
     * @param Zend_Translate $translator
     * @return ORMForm
     */
    public static function getORMFormInstance(ORM_DAO $ORM,$options=null,Zend_Translate $translator = null){

        //todo cache form object
        $form = new self();
        $form->init();
        $tableFeild = $form->_setFormOrderByTableField($ORM);
        $form->autoForm($ORM,$options,$tableFeild);
        //end cache
        
        isset($translator)&&$form->setTranslator($translator);
        
        $form->bindORM($ORM,null,$tableFeild,$form->fillFormFiled($tableFeild,$ORM));
        
        return $form;
    }
    
    /**
     * 根据数据表的字段来设定表单顺序
     * @param EasyORM $ORM
     */
    private function _setFormOrderByTableField(ORM_DAO $ORM){
        $tableFeild = $ORM->getSelectField();
        if(empty($tableFeild)){
            $tableFeild = $this->_queryDAOField($ORM);
            $this->_useMySort = true; //用设定的排序方式替代Zend默认排序方式
        }else{
            $tableFeild = $this->_formatFeild($tableFeild);
            $this->_useMySort = true; //用设定的排序方式替代Zend默认排序方式
        }
        return $tableFeild;
    }
    
    private function _formatFeild($tableFeild){
    	$feildArray = explode(',', $tableFeild);
    	foreach ($feildArray as &$feild) {
    		$feild = trim($feild);
    		if(preg_match('/\w+\s+as\s+(\w+)/i', $feild ,$matchs)){
    			$feild = $matchs[1];
    		}else{
    			$feild = str_replace('.','_',$feild);
    		}
    	}
    	return implode(',',$feildArray);
    }
    
    /**
     * 获取DAO对应的表的字段
     * @param ORM_DAO $DAO
     */
    private function _queryDAOField(ORM_DAO $DAO){
        $temp = $DAO->getORM()->query("describe $DAO");
        $field = array();
        foreach ($temp as $item){
            $field[] = $item['Field'];
        }
        return implode(',',$field);
    }
    
    /**
     * 添加一个ORM到表单
     * @param EasyORM $ORM
     * @param Array|Zend_Config $options
     */
    public function addORM(ORM_DAO $ORM,$options = null){
        $tableFeild = $this->_setFormOrderByTableField($ORM);
        $this->autoForm($ORM,$options,$tableFeild);
        $this->bindORM($ORM,null,$tableFeild,$this->fillFormFiled($tableFeild,$ORM));
        
        return $this;
    }
    
    /**
     * 填充指定的表单域
     * @param $formFeild
     * @param $ORM
     * @param $db
     */
    protected function fillFormFiled($formFeild,ORM_DAO $ORM){
        $filterData = $ORM->getFilterField();
       if(!empty($filterData)){
            $formData = $ORM->get();
            $formData = $formData[0];
            if(empty($formData)){
                $ORM->unsetFilterField(); //将ORM重置为插入操作
            }else{
                $this->_cacheAble = false; //标记当前form不可缓存
            }
        }
        
        $translator = $this->getTranslator();
        foreach (explode(',',$formFeild) as $name){
            $element = $this->getElement($name);
            if(!($element instanceof Zend_Form_Element)) continue;
            $element->setLabel(isset($translator)&&$translator->isTranslated($name)?$translator->translate($name):$name);
            if(isset($translator)&&$translator->isTranslated($name.'_explaininfo')){
                $element->setDescription($translator->translate($name.'_explaininfo'));
            }
            isset($formData[$name])&&$element->setValue($formData[$name]);
        }
        
        return $formData;
    }
    
    /**
     * 根据ORM自动生成表单
     * @param EasyORM $ORM
     * @param Array $options
     * $options = array(
     *      'property'      => array(
     *              'element'       => 'text',
     *              'options'       => array()
     *      )
     * )
     * @return ORMForm
     */
    protected function autoForm(ORM_DAO $ORM,$options=null,$tableFiled){
        $fileds = explode(',',$tableFiled);
        $element=null;$name='';$formElements=array();
        
        foreach ($fileds as $name){
            $type=isset($options[$name]['type'])?$options[$name]['type']:'text';
            $this->_elementOrder++;
            if(!isset($options[$name]['order'])){
            	$options[$name]['order'] = $this->_elementOrder;
            }
            $element=$this->createElement($type,$name,isset($options[$name])?$options[$name]:null);
            array_push($formElements,$element);
        }
        $this->addElements($formElements);
        return $this;
    }
    
    /**
     * 设定是否采用自定义的排序方式
     * @param boolean $flag
     * @return ORMForm
     */
    public function setUseMySort($flag){
        $this->_useMySort = $flag;
        return $this;
    }
    
    /**
     * 获取是否采用了自定义的排序方式
     */
    public function getIsUseMySort(){
        return $this->_useMySort;
    }
    
    /**
     * Form元素的排序方法
     * 为什么要重写_sort方法？ 
     * Zend_Form排序方式： 
     *      {默认：[user,password,address,phone,zip]}->
     *      {设定zip的order为3}->
     *      {结果：［user,password,zip,phone］}
     * ORMFORM的排序方式：
     *      {结果：[user,password,zip,address,phone]}
     *
     */
    protected function _sort(){
        if(!isset($this->_useMySort)){
            return;
        }
        if($this->_useMySort){
            if ($this->_orderUpdated) {
            	asort($this->_order);
            	$pos = 0;
            	$currentOrder = null;
            	foreach ($this->_order as &$item){
            		if($currentOrder === $item){
            			$pos++;
            		}
            		$currentOrder = $item;
            		$item+=$pos;
            	}
                $this->_orderUpdated = false;
                
            }
        }else{
            parent::_sort();
        }
    }
    
    /**
     * 设置提交按钮
     * @param String|Boolean $submitLable ,
     */
    public function setSubmit($submitLable = null){
        if($this->_autoSubmit){
            $this->_autoSubmit = false; //禁用自动添加提交按钮
            if($submitLable === false ) return $this;
            if(!isset($submitLable)){
                $translator = $this->getTranslator();
                $submitLable = isset($translator)&&$translator->isTranslated('formSubmit')?$translator->translate('formSubmit'):'formSubmit';
            }
            $this->addElement($this->createElement('Submit','formSubmit',array('label' => $submitLable))->setOrder(++$this->_elementOrder));
        }
        return $this;
    }
    
    /**
     * 
     * 添加表单来源地址的隐藏域
     */
    public function addReferer(){
    	$this->addElement($this->createElement('hidden','formReferer',array('value' => base64_encode($_SERVER['HTTP_REFERER']),'Decorators'=>array('ViewHelper')))->setOrder(++$this->_elementOrder));
    }
    
    /**
     * 获取表单的来源地址
     */
    public function getReferer(){
    	$refer = htmlentities($_POST['formReferer']);
    	return empty($refer)?$_SERVER['HTTP_REFERER']:base64_decode($refer);
    }
    
    public function render(Zend_View_Interface $view = null){
        if($_POST||!$this->_cacheAble){
            $this->_autoSubmit&&$this->setSubmit();
            $this->addReferer();
            return parent::render($view);
        }
        
        $formRenderCacheKey = md5(serialize($this->getAttribs()+$this->getElements()));
        if(!$content = $this->_cache->get($formRenderCacheKey)){
            $this->_autoSubmit&&$this->setSubmit();
            $this->addReferer();
            $content = parent::render($view);
            $this->_cache->set($formRenderCacheKey,$content,3600*12);
        }
        return $content;
    }
    
    public function isValid($data){
        $valid = parent::isValid($data);
        
         //判断是否重复提交表单
        if($valid){
            $dataKey = md5(serialize($_POST).serialize($_FILES));
            $hashKey = session_id();
            if($dataKey==$this->_cache->get($hashKey)){
                  $valid = false;
                  $this->addDecorator('Errors');
                  $translator = $this->getTranslator();
                  $this->addError(
                    ($translator&&$translator->isTranslated(self::DUPLICATE_SUBMIT))?
                    $translator->translate(self::DUPLICATE_SUBMIT):'您不能重复提交'
                    );
            }else{
                $this->_cache->set($hashKey,$dataKey,1200);
            }
        }
        
        return $valid;
    }
    
	public function getElementValue($key,$index = 0){
		return $this->ORMList[$index]['data'][$key];
	}
    
    /**
     * @return ORM_Form
     * @param $view
     */
    public function assignTo(Zend_View_Abstract $view){
        $view->form = $this;
        return $this;
    }
    
    /**
     * 
     * 注册事件函数
     * @param string $event
     * @param $function
     */
    public function registerEvent($event,$function){
    	$this->_eventList[$event] = $function;
    }
    
    /**
     * 
     * 注销事件函数
     * @param string $event
     */
    public function removeEvent($event){
    	unset($this->_eventList[$event]);
    }
    
    protected function handleEvent($event){
    	if(isset($this->_eventList[$event])){
            $fun = $this->_eventList[$event];
            $fun();
        }
    }
    
    /**
     * 初始化操作
     */
    public function init(){
        $this->_cache = Application::getInstance();
        $this->_cache->setNameSpace('ORM_Form');
    }
    
}
