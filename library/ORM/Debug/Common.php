<?php
/**
 * @package ORM
 */
require_once 'Interface.php';
class ORM_Debug_Common implements ORM_Debug_Interface{
    public function debug($sql){
        echo $sql;
    }
}