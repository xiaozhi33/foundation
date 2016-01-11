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
require_once dirname(__FILE__).'/IModifier.php';

/**
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: Modifier.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class Modifier implements IModifier  {

    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_name  = "";
    private $_count = 0;
    private $_value = "";
    private $_documentRatio = 0.0;
    private $_attribute = "";
    private $_navigator = null;
    private $_isInterval = false;

    //--------------------------------------------------------------------------
    //---                          constructor                               ---
    //-------------------------------------------------------------------------- 
    /**
     * Constructor.
     * @param string $name
     * @param int $count
     * @param string $value
     * @param float $documentRatio
     * @param string $attribute
     * @param boolean $isInterval
     * @param INavigator $navigator
     * @return Modifier
     */
    public function __construct($name, $count, $value, $documentRatio, 
        $attribute, $isInterval=false, $navigator=null) {
        $this->_name = (string)$name;
        $this->_count = (int)$count;
        $this->_value = (string)$value;
        $this->_documentRatio = (float)$documentRatio;
        $this->_attribute = (string)$attribute;
        $this->_navigator = $navigator;
        if (is_bool($isInterval)) {
            $this->_isInterval = $isInterval; 
        } else {
            $this->_isInterval = false;
        }
    }
    
    //--------------------------------------------------------------------------
    //---                        public functions                            ---
    //-------------------------------------------------------------------------- 
    public function getName() {
        return $this->_name;
    }
    
    public function setName($name) {
        $this->_name = $name;
    }
    
    public function getCount() {
        return $this->_count;
    }
    
    public function getValue() {
        return $this->_value;
    }

    public function getDocumentRatio() {
        return $this->_documentRatio;
    }

    public function getAttribute() {
        return $this->_attribute;
    }
          
    public function getNavigator() {
        return $this->_navigator;    
    }
     
    public function attach(INavigator $navigator) {
        // TODO do real attach here -> add modifier to navigator?
        $this->_navigator = $navigator;
    }
    
    public function detach() {
        // TODO do a real detach -> remove modifier (this) from navigator ???
        return new Modifier($this->_name, $this->_count, $this->_value, 
            $this->_documentRatio, $this->_attribute, $this->_isInterval, null);    
    }

    public function isDetached() {
        if ($this->_navigator == null) {
            return true;        
        } else {
            return false;
        }
    }

    public function isInterval() {
        return $this->_isInterval;    
    }
    
    public function prepareUnset() {
        
        // remove navigator reference
        $this->_navigator = null;
    }
    
}
