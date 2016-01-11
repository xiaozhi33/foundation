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
 * A type that represents a pair of an {@link IModifier} and a {@link ModifyMode}. 
 * Used as components of {@link IAdjustmentGroup}s in and {@link INavigation}.
 * 
 * @package sde_fastsearch_navigation
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: IAdjustment.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface IAdjustment {
    
    /**
     * Returns the {@link IAdjustmentGroup} of this adjustment.
     * @return IAdjustmentGroup
     */
    public function  getAdjustmentGroup();

    /**
     * Returns the filter term produced by this adjustment alone, 
     * with optional +/- prefix.
     * @param boolean $includePrefix
     * @return string
     */
    public function getFilterTerm($includePrefix=true);
          
    /**
     * Returns the index of this adjustment in its {@link IAdjustmentGroup}.
     * @return int
     */
    public function getIndex();
    
    /**
     * Returns the {@link IModifier} of this adjustment.
     * @return IModifier
     */
    public function getModifier();
          
    /**
     * Returns the modifier name of this adjustment.
     * @return string
     */
    public function getModifierName();
          
    /**
     * Returns the modifier value of this adjustment.
     * @return string
     */
    public function getModifierValue();
    
    /**
     * Returns the {@link ModifyMode} for this adjustment.
     * @return int
     */
    public function getModifyMode();

}
