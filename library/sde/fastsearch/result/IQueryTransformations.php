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
require_once dirname(__FILE__).'/IResultObject.php';

/**
 * Provides methods to find named modifications or suggestions contained 
 * in the query result. A query transformation can be one of two types: 
 * a modifications or a suggestion. A modification results in changes
 * to the query that is submitted to the search engine while a suggestion 
 * leaves the query unchanged and is merely returned together with the result as 
 * information on how to potentially improve the query.
 * 
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: IQueryTransformations.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface IQueryTransformations extends IResultObject {
    /**
     * Returns an ArrayObject containing all the query transformations in this 
     * query transformations.
     * @return ArrayObject
     */ 
    public function getAllQueryTransformations();

    /**
     * Returns a query transformation done by given query transformer of all 
     * types (not only modifications and suggestions)
     * @param string
     * @return IQueryTransformation 
     */
    public function getTransformation($name);
    
    /**
     * Returns all query transformations done by given query transformer of all 
     * types (not only modifications and suggestions)
     * @param string
     * @return ArrayObject
     */
    public function getTransformationsByName($name);
    
    /**
     * Returns the final modification done by a given query transformer.
     * @param string name
     * @return IQueryTransformation
     */
    public function getModification($name);

    /**
     * Returns an ArrayObject containing all the query transformations in this 
     * query transformations that are modifications.
     * @return ArrayObject
     */
    public function getModifications();
              
     /**
      * Returns all the modifications done by a given query transformer.
      * @param string $name
      * @return ArrayObject
      */
    public function getModificationsByName($name);

     /**
      * Returns the query that was submitted to the search engine
      * @return string
      */
    public function  getSubmittedQuery();
              
     /**
      * Returns the final suggestion returned from a given query transformer.
      * @return IQueryTransformation
      */
    public function getSuggestion($name);
     
     /**
      * Returns an ArrayObject containing all the query transformations in this 
      * query transformations that are suggestions.
      * @return ArrayObject
      */         
    public function getSuggestions();
              
     /**
      * Returns all the suggestions returned by a given query transformer.
      * @param string $name
      * @return ArrayObject 
      */
    public function getSuggestionsByName($name);
              
     /**
      * Returns true if this query transformations contain a query 
      * transformation named {@link $name}.
      * @param string $name
      * @return boolean
      */
    public function hasTransformation($name);
     
    /**
     * Returns true if this query transformations contain a query 
     * transformation named {@link $name} and this query transformation is a modification.
     * @param string $name
     * @return boolean
     */
    public function hasModification($name);
              
    /**
     * Returns true if this query transformations contain a query 
     * transformation named {@link $name} and this query transformation is a suggestion.
     * @param string $name
     * @return boolean
     */
    public function hasSuggestion($name);
}
