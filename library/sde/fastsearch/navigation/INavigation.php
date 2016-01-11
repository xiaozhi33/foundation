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
 * A type representing a state of navigation. An {@link INavigation} can specify several 
 * {@link IAdjustmentGroup}s, each associated to exactly one {@link INavigator}. Each group 
 * consists of a set of {@link IAdjustment}s, each of which specifies one {@link IModifier}  
 * and a {@link ModifyMode}.
 *
 * The suggested usage of this interface is to use it to maintain the history of 
 * navigation choices performed by a user. When a new query is to be submitted, 
 * submit this {@link INavigation} object together with the query to the search method 
 * in {@link ISearchView}.
 * 
 * It is suggested that the application programmer keep the base query. Accept 
 * new choices from the user, and change the {@link INavigation} object accordingly, 
 * before applying it again to the base query. This will contain navigation 
 * logic to this class.
 * 
 * @package sde_fastsearch_navigation
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: INavigation.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface INavigation {
   
    /**
     * Adds a new {@link IAdjustmentGroup} for the given {@link INavigator}.
     * @return IAdjustmentGroup
     */
    public function add(INavigator $navigator);

    /* java interface
    // Add a new IAdjustmentGroup for the given navigator name.
    // public function add(java.lang.String navigatorName, java.lang.String navigatorAttribute)
    */    

    /**
     * Instruments the given {@link IQuery} with the navigation information in this 
     * {@link INavigation} instance.
     * @param IQuery $query
     */
    public function instrument(IQuery $query);
    
    /**
     * Clears contents of this navigation.
     */
    public function clear();
          
    /**
     * Checks for {@link IAdjustmentGroup}s associated to the named {@link INavigator}.
     * @param string $navigatorName
     * @return boolean
     */
    public function containsGroups($navigatorName);
          
    /**
     * Returns the nth {@link IAdjustmentGroup} associated to the named {@link INavigator}. If
     * no {@link $n} is given, the first {@link IAdjustmentGroup} will be returned.
     * @param string $navigatorName
     * @param int $n
     * @return IAdjustmentGroup
     */  
    public function getGroup($navigatorName, $n=0);
          
    /**
     * Returns the {@link IAdjustmentGroup}s associated to the named {@link INavigator}.
     * @return array
     */
    public function  getGroups($navigatorName);
          
    /**
     * Checks for content in this navigation.
     * @return boolean
     */
    public function isEmpty();
          
    /**
     * Iterates over the {@link IAdjustmentGroup}s in this navigation.
     * @param string $navigatorName
     * @return Iterator
     */
    public function iterator($navigatorName="");
          
    /**
     * Removes the given {@link IAdjustmentGroup}.
     * @param IAdjustmentGroup $group
     */
    public function remove(IAdjustmentGroup $group);
          
    /**
     * Removes the {@link IAdjustmentGroup}s associatd to the named {@link INavigator}.
     * @param string $navigatorName;
     */
    public function removeByName($navigatorName);
    
    /**
     * Returns the number of {@link IAdjustmentGroup}s.
     * @param string $navigatorName
     * @return int
     */
    public function size($navigatorName="");
}
