<?php
// -----------------------------------------------------------------------------
// sdefastsearch - A PHP Fast ESP Search API
// Copyright (C) 2008,2009 sueddeutsche.de GmbH, Hultschiner Str. 8, D-81677 München
// www.sueddeutsche.de.de - Andreas.Scheerer@sueddeutsche.de
//
// This file is part of sdefastsearch.
//
// sdefastsearch is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------

// includes
require_once dirname(__FILE__).'/IQueryTransformation.php';

/**
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: QueryTransformation.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class QueryTransformation implements IQueryTransformation {

    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_action = "";
    private $_message = "";
    private $_custom = "";
    private $_messageid = 0;
    private $_name = "";
    private $_query = "";

    //--------------------------------------------------------------------------
    //---                          constructor                               ---
    //-------------------------------------------------------------------------- 
    /**
     * Constructor.
     * @param string $name
     * @param string $action
     * @param string $query
     * @param string $custom
     * @param string $message
     * @param int $messageid
     */
    public function __construct($name, $action, $query, $custom, $message,
        $messageid) {
        $this->_name      = (string)$name;
        $this->_action    = (string)$action;
        $this->_query     = (string)$query;
        $this->_custom    = (string)$custom;
        $this->_message   = (string)$message;
        $this->_messageid = (int)$messageid;        
    }

    //--------------------------------------------------------------------------
    //---                        public methods                              ---
    //-------------------------------------------------------------------------- 
    public function getAction() {
        return $this->_action;
    }
    
    public function getMessage() {
        return $this->_message;
    }

    public function getCustom() {
        return $this->_custom;
    }
         
    public function getMessageID() {
        return $this->_messageid;
    }

    public function getName() {
        return $this->_name;
    }

    public function getQuery() {
        return $this->_query;
    }
    
    public function prepareUnset() {
        // no child references 
        // -> nothing to be done here
    }
    
}
