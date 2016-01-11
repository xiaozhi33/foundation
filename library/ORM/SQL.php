<?php
/**
 * @package ORM
 *
 */
class ORM_SQL {
    private $_sql;
    public function __construct($sql){
        $this->_sql = $sql;
    }
    
    public function __toString(){
        return $this->_sql;
    }
}