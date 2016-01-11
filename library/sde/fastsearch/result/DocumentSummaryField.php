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
require_once dirname(__FILE__).'/IDocumentSummaryField.php';

/**
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: DocumentSummaryField.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class DocumentSummaryField implements IDocumentSummaryField  {
    
    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_type  = 0;
    private $_name  = "";
    private $_value = ""; 
    
    //--------------------------------------------------------------------------
    //---                         constructor                                ---
    //-------------------------------------------------------------------------- 
    /**
     * @param string $name 
     * @param mixed  $value
     * @param int    $type
     * @return DocumentSummaryField
     */
    public function __construct($name, $value, $type=FieldType::UNKNOWN) {
        $this->_name  = $name;
        if (FieldType::isValidType($type)) {
            $this->_type = $type;
        } else {
            $this->_type = FieldType::UNKNOWN;
        }
        switch ($type) {
            case FieldType::STRING:
                $this->_value = (string)$value;
                break;
            case FieldType::TEXT:
                $this->_value = (string)$value;
                break;
            case FieldType::INTEGER:
                $this->_value = (int)$value;
                break;
            case FieldType::DOUBLE:
            case FieldType::FLOAT:
                $this->_value = (float)$value;
                break;
            default:
                $this->_value = $value;
                break;
        }
    }
    
    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    public function getName() {
        return $this->_name;
    }

    public function getValue() {
        return $this->_value;
    }
    
    public function getStringValue() {
        return (string)$this->_value;
    }
    
    public function getType() {
        return $this->_type;
    }
    
    public function isEmpty() {
        return empty($this->_value);
    }
    
    public function prepareUnset() {
        // no child references 
        // -> nothing to be done here
    }
}
