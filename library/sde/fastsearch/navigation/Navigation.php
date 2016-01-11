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
require_once dirname(__FILE__).'/INavigation.php';
require_once dirname(__FILE__).'/AdjustmentGroup.php';

/**
 * @package sde_fastsearch_navigation
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: Navigation.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class Navigation implements INavigation {

    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_groups = null;
    private $_groupsByName = array();
    private $_indexCount = 0;
    private $_indexNavCount = 0;

    //--------------------------------------------------------------------------
    //---                           constructor                              ---
    //-------------------------------------------------------------------------- 
    /**
     * Constructor.
     */ 
    public function __construct() {
        $this->_groups = new ArrayObject();
    }
    
    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    public function add(INavigator $navigator) {
        $adjustmentGroup = new AdjustmentGroup($this, $navigator, 
            $this->_indexCount, $this->_indexNavCount);
        $this->_groups->offsetSet($this->_indexCount, $adjustmentGroup);
        
        if (!array_key_exists($navigator->getName(),$this->_groupsByName)) {
            $this->_groupsByName[$navigator->getName()] = new ArrayObject(); 
        }
        
        $this->_groupsByName[$navigator->getName()]
            ->offsetSet($this->_indexNavCount, $adjustmentGroup);
        $this->_indexCount++;
        $this->_indexNavCount++;
        return $adjustmentGroup;
    }

    /* java interface
    // Add a new IAdjustmentGroup for the given navigator name.
    // public function add(java.lang.String navigatorName, java.lang.String navigatorAttribute)
    */    

    public function instrument(IQuery $query) {
        $iter = $this->iterator();
        $navParamValue = "";
        while($iter->valid()) {
            $adjustmentgroup = $iter->current();
            $navigatorAttr = $adjustmentgroup->getNavigatorAttribute();
            
            $adjIter = $adjustmentgroup->iterator();
            while ($adjIter->valid()) {
                $adjustment = $adjIter->current();
                //$adjName  = $adjustment->getModifierName();
                $adjValue = $adjustment->getModifierValue();
                $adjMode  = $adjustment->getModifyMode();
                
                if (!empty($navParamValue)) {
                    $navParamValue .=",";
                }
                
                $navParamValue .= ModifyMode::getModeString($adjMode);
                $navParamValue .= $navigatorAttr.":";
                $navParamValue .= $adjValue; 
                                
                $adjIter->next();
            }
            
            $iter->next();
        }
        $query->setParameterByType(BaseParameter::NAVIGATION_FILTER, $navParamValue);
    }
    
    public function clear() {
        $this->_groups = new ArrayObject();
        $this->_indexCount   = 0;
        $this->_groupsByName = array();
    }
          
    public function containsGroups($navigatorName) {
        return array_key_exists($navigatorName, $this->_groupsByName);
    }
          
    public function getGroup($navigatorName, $n=0) {
        $keys = array_keys($this->_groupsByName[$navigatorName]);
        if ($n>0) $n -= 1;
        return $this->_groupsByName[$navigatorName]->offsetGet($keys[$n]);
    }
          
    public function getGroups($navigatorName) {
        return $this->_groupsByName[$navigatorName];
    }
          
    public function isEmpty() {
        if ($this->_groups->count() > 0) { 
            return false;
        } else { 
            return true;
        }
    }
          
    public function iterator($navigatorName="") {
        if (empty($navigatorName)) {
            return $this->_groups->getIterator();
        } else {
            return $this->_groupsByName[$navigatorName]->getIterator();
        }
    }
          
    public function remove(IAdjustmentGroup $group) {
        $index = $group->getIndex();
        $indexNavigator = $group->getIndex(true);
        $navigatorName  = $group->getNavigatorName();
        $this->_groups->offsetUnset($index);
        $this->_groupsByName[$navigatorName]->offsetUnset($indexNavigator);        
    }
          
    public function removeByName($navigatorName) {
        $groups = $this->_groupsByName[$navigatorName]->getArrayCopy();
        foreach ($groups as $group) {
            $index = $group->getIndex();
            $this->_groups->offsetUnset($index);
        }
        unset($this->_groupsByName[$navigatorName]);
    }
    
    public function size($navigatorName="") {
        if (empty($navigatorName)) {
            return $this->_groups->count();
        } else {
            return $this->_groupsByName[$navigatorName]->count(); 
        }
    }
}
