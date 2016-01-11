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

/**
 * Represents a search parameter used by an {@link IQuery}.
 * 
 * @package sde_fastsearch_query
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: SearchParameter.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class SearchParameter {

    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_type;
    private $_name;
    private $_value;
        
    //--------------------------------------------------------------------------
    //---                         constructor                                ---
    //-------------------------------------------------------------------------- 
    /**
     * @param int    $paramType
     * @param mixed  $paramValue
     * @param string $paramName 
     */
    public function __construct($paramType,$paramValue,$paramName="") {
        if (BaseParameter::isValidParameter($paramType) && $paramType != 0) {
            $this->_type = $paramType;
            $this->_name = BaseParameter::getParameterString($paramType);
        } else {
            $this->_type = BaseParameter::UNKNOWN;
            $this->_name = (string)$paramName;
        }
        $this->_value = $paramValue;
    }
    
    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    /**
     * Returns the name of this parameter.
     * 
     * @return string
     */
    function getName() {
        return $this->_name;
    }
    
    /**
     * Returns the value of this parameter.
     * 
     * @return mixed
     */
    function getValue() {
        return $this->_value;
    }

    /**
     * Sets the value of this parameter which can be of any type. 
     * Be careful: In the resulting http request the method {@link getStringValue()} 
     * is used to add this value.
     *  
     * @param mixed $value
     */
    function setValue($value) {
        $this->_value = $value;
    }
        
    /**
     * Returns the string value of this parameter.
     * 
     * @return string
     */
    function getStringValue() {
        return (string)$this->_value;
    }
    
    /**
     * Returns an integer representing the parameter type. Valid parameter types
     * are defined in {@link BaseParameter}  
     * 
     * @return int
     */
    function getType() {
        return $this->_type;
    }
}
