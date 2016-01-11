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
require_once dirname(__FILE__).'/IAdjustmentGroup.php';
require_once dirname(__FILE__).'/ModifyMode.php';
require_once dirname(__FILE__).'/Adjustment.php';

/**
 * @package sde_fastsearch_navigation
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: AdjustmentGroup.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class AdjustmentGroup implements IAdjustmentGroup {
    
    //--------------------------------------------------------------------------
    //---                        private fields                              ---
    //-------------------------------------------------------------------------- 
    private $_navigation = null;
    private $_navigator = null;
    private $_adjustments = null;
    private $_indexCount  = 0;
    private $_index = 0;
    private $_indexNav = 0;
    private $_nameMap = null;
    
    //--------------------------------------------------------------------------
    //---                           constructor                              ---
    //-------------------------------------------------------------------------- 
    /**
     * Constructor.
     * 
     * @param INavigation $navigation
     * @param int $index Index of containing navigation
     * @param int $indexNav Index of containing navigator name
     */
    public function __construct(INavigation $navigation, INavigator $navigator,
        $index, $indexNav) {
        $this->_adjustments = new ArrayObject();
        $this->_nameMap = array();
        $this->_index = 0;
        $this->_indexNav = 0;
        $this->_navigation = $navigation;
        $this->_navigator = $navigator;
    }

    //--------------------------------------------------------------------------
    //---                           public methods                           ---
    //-------------------------------------------------------------------------- 
    public function add(IModifier $modifier, $modifyMode=0) {
        if ($modifyMode == 0) {
            $modifyMode = ModifyMode::MODE_INCLUDE;
        }
        $adjustment = new Adjustment($this, $modifier, $modifyMode, $this->_indexCount);
        $this->_adjustments->offsetSet($this->_indexCount, $adjustment);
        $this->_nameMap[$modifier->getName()] = $this->_indexCount;
        $this->_indexCount++;
        return $adjustment;
    }

    public function clear() {
        $this->_adjustments = new ArrayObject();
        $this->_indexCount = 0;
        $this->_nameMap = array();
    }

    public function getFilterTerm() {
        // TODO return real filter term
        return "";
    }
    
    public function get($modifierName) {
        return $this->_adjustments->offsetGet($this->_nameMap[$modifierName]);    
    }
    
    public function getIndex($useNavIndex=false) {
        if ($useNavIndex) {
            return $this->_indexNav;
        } else {
            return $this->_index;
        }
    }

    public function getNavigation() {
        return $this->_navigation;
    }
    
    public function getNavigator() {
        return $this->_navigator;
    }

    public function getNavigatorAttribute() {
        return $this->_navigator->getFieldName();
    }
    
    public function getNavigatorName() {
        return $this->_navigator->getName();
    }
          
    public function isEmpty() {
        if ($this->_adjustments->count()==0) {
            return true;
        } else {
            return false;
        }
    }
          
    public function iterator() {
        return $this->_adjustments->getIterator();
    }

    public function remove(IAdjustment $adjustment) {
        $adjIndex = $adjustment->getIndex();
        $this->_adjustments->offsetUnset($adjIndex);
        unset($this->_nameMap[$adjustment->getModifierName()]);
    }
          
    public function removeByName($modifierName) {
        $adjIndex = $this->_nameMap[$modifierName];
        $this->_adjustments->offsetUnset($adjIndex);
        unset($this->_nameMap[$modifierName]);
    }
          
    public function size() {
        return $this->_adjustments['count'];
    }
}
