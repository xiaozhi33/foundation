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
require_once dirname(__FILE__).'/IAdjustment.php';

/**
 * @package sde_fastsearch_navigation
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: Adjustment.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class Adjustment implements IAdjustment {

    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_adjustmentGroup = null;
    private $_modifier = null;
    private $_modifyMode = 0;
    private $_index = -1;
    
    //--------------------------------------------------------------------------
    //---                           constructor                              ---
    //-------------------------------------------------------------------------- 
    /**
     * Constructor.
     * 
     * @param $adjustmentGroup
     * @param $modifier
     * @param int $modifyMode
     * @param int $index
     */
    public function __construct(IAdjustmentGroup $adjustmentGroup,
        IModifier $modifier, $modifyMode, $index) {
        $this->_adjustmentGroup = $adjustmentGroup;
        $this->_modifier = $modifier;
        $this->_modifyMode = $modifyMode;
        $this->_index = $index;
    }
    
    //--------------------------------------------------------------------------
    //---                        public methods                              ---
    //-------------------------------------------------------------------------- 
    public function getAdjustmentGroup() {
        return $this->_adjustmentGroup;
    }
    
    public function getFilterTerm($includePrefix=true) {
        return "";
    }
    
    public function getIndex() {
        return $this->_index;
    }
    
    public function getModifier() {
        return $this->_modifier;
    }
          
    public function getModifierName() {
        return $this->_modifier->getName();
    }
          
    public function getModifierValue() {
        return $this->_modifier->getValue();
    }
    
    public function getModifyMode() {
        return $this->_modifyMode;
    }
}
