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
 * A type that represents a selection of {@link IAdjustment}s, which are logically OR'ed. 
 * Each adjustment group is in turn AND'ed with the other adjustment groups of an 
 * {@link INavigation} instance.
 * 
 * @package sde_fastsearch_navigation
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: IAdjustmentGroup.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface IAdjustmentGroup  {
    /**
     * Adds an {@link IAdjustment} to this group.
     * 
     * @param IModifier $modifier
     * @param int $modifyMode
     * @return IAdjustment
     */
    public function add(IModifier $modifier);

    /**
     * Clears this adjustment group.
     */
    public function clear();

    /**
     * Retruns the filterterm produced by this adjustment group alone.
     *
     * @return string
     */
    public function getFilterTerm();
    
    /**
     * Returns the {@link IAdjustment} associated to the named {@link IModifier}.
     *
     * @return IAdjustment
     */
    public function get($modifierName);
    
    /**
     * Returns current index of this adjustment group in contaning {@link INavigation}, 
     * grouped by {@link INavigator}.
     *      
     * @return int
     */
    public function getIndex();

    /**
     * Returns containing {@link INavigation}.
     * 
     * @return INavigation
     */
    public function getNavigation();
    
    /**
     * Returns the associated {@link INavigator}.
     * 
     * @return INavigator
     */
    public function getNavigator();

    /**
     * Returns the associated navigator attribute.
     * @return string
     */ 
    public function getNavigatorAttribute();
    
    /**
     * Returns the associated navigator name.
     * @return string
     */
    public function getNavigatorName();
          
    /**
     * Returns true if there are no {@link IModifier}s.
     * @return boolean
     */
    public function isEmpty();
          
    /**
     * Iterates over the {@link IAdjustment}s in this group.
     * @return Iterator
     */
    public function iterator();

    /**
     * Removes the given {@link IAdjustment}.
     */
    public function remove(IAdjustment $adjustment);
          
    /**
     * Removes the {@link IAdjustment} associated to the named {@link IModifier}.
     */
    public function removeByName($modifierName);
          
    /**
     * Returns the count of {@link IModifier}s.
     * @return int
     */
    public function size();
    
    // java interface
    /*
    Add an IAdjustmentt with a given ModifyMode, to this group.
    public function addByMode($mode, IModifier $modifier);
    
    //Add an IAdjustment with a given ModifyMode, to this group. 
    IAdjustment    add(ModifyMode mode, java.lang.String name, java.lang.String value)
    
    //Add an IAdjustment to this group.      
    IAdjustment    add(java.lang.String name, java.lang.String value)
          
    */      
}
?>