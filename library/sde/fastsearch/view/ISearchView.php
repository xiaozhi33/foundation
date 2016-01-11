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
 * @package sde_fastsearch_view
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: ISearchView.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface ISearchView {
    /**
     * Returns the name of this search view.
     * @return string
     */
    public function getName();
    
    /**
     * Returns a description of this search view.
     * @return string
     */
    public function getDescription();
  
    /**
     * Returns the time the view was created as unix timestamp.
     * @return int Unix timestamp
     */
    public function getCreated();
    
    /**
     * Returns the time the view was saved the last time as unix timestamp.
     * @return int Unix timestamp
     */
    public function getLastSaved();
    
    /**
     * Internal initialize method.
     * @param ISearchEngine $engine
     */
    public function initEngine(ISearchEngine $engine);
    
    /**
     * Submits a query and returns the query result as an {@link IQueryResult} 
     * interface.
     * @param IQuery $query
     * @return IQueryResult
     */
    public function search(IQuery $query);
}
