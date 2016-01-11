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
 * Represents a query that can be submitted to the search engine. 
 * This includes both the actual query string and all search parameters 
 * that should be used.
 * 
 * @package sde_fastsearch_query
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: IQuery.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface IQuery {
    /**
     * Sets the query string of this query.
     * 
     * @param string $queryString
     */
    public function setQueryString($queryString); 

    /**
     * Returns the query string of this query.
     * 
     * @return string
     */
    public function getQueryString();

    /**
     * Sets the given {@link SearchParameter} for this query.
     * 
     * @param SearchParameter $param
     */    
    public function setParameter(SearchParameter $param);

    /**
     * Sets the value of the parameter represented by the given parameter 
     * type for this query. The given parameter type should be a valid 
     * type defined in {@link BaseParameter}.
     * 
     * @see BaseParameter
     * @param int    $paramType
     * @param string $value
     */
    public function setParameterByType($paramType, $value);

    /**
     * Sets the value of the parameter with the given parameter name
     * for this query. 
     *  
     * @param string $paramName
     * @param string $value
     */
    public function setParameterByName($paramName, $value);

    /**
     * Returns all search parameters in this query.
     * 
     * @return array
     */
    public function getSearchParameters();
    
    /**
     * Returns the search parameter with given name. If no such parameter exists
     * a {@link NoSuchParameterException} is thrown.
     * 
     * @param string $paramName
     * @return SearchParameter
     * @throws {@link NoSuchParameterException}
     */
    public function getParameterByName($paramName);
    
    /**
     * Returns the search parameter of the given type. If no such parameter exists
     * a {@link NoSuchParameterException} is thrown.
     * 
     * @param int $paramType
     * @throws {@link NoSuchParameterException}
     */
    public function getParameterByType($paramType);
    
    /**
     * Adds a term to the filter parameter in this query.
     * 
     * @param string $term
     */
    public function addFilterTerm($term);
}
