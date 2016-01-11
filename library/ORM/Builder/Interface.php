<?php

/**
 * 
 * @package ORM
 * @category Builder
 *
 */
interface ORM_Builder_Interface {
    public function selectSql(ORM_DAO $dao);
    
    public function updateSql(ORM_DAO $dao);
    
    public function insertSql(ORM_DAO $dao);
    
    public function deleteSql(ORM_DAO $dao);
}