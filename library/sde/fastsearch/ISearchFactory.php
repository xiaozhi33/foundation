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
 * ISearchFactory is a factory used to create the {@link ISearchView} instances. 
 * To instantiate the factory interface, use {@link SearchFactory}.
 * 
 * @package sde_fastsearch
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: ISearchFactory.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface ISearchFactory {
  
    /**
     * Returns the search view with the specified name, with the option of 
     * forcing a reload of the view from the qrserver.
     *  
     * @param string $name
     * @param boolean $reload
     * @return ISearchView
     * @throws {@link SearchEngineException}
     */
    public function getSearchView($name='default', $reload=false);

    /**
     * Returns the names of all existing search views in FAST ESP.
     * 
     * @return array
     * @throws {@link SearchEngineException}
     */
    public function getSearchViewList();
    
    /**
     * Sets the names of all existing search views in FAST ESP to suppress request
     * for retrieving these search view names. Be careful: Setting invalid views 
     * here will result in invalid search requests. Best practice is to cache the 
     * availabe search view list after the first search request and set it for 
     * further search requests using this method.
     * 
     * @param array $viewList List of available search views.
     */
    public function setSearchViewList(array $viewList);

        
    /**
     * Sets the properties for the used search engine.
     *
     * @param array $properties
     */
    public function setProperties(array $properties);
    
}
