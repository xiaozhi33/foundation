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
 * Represents the result of a query. The query result contains 
 * query transformations (changed performed on the original query before 
 * submission) and the document summaries of the documents matching the 
 * query. The query transformations contain feedback from query transformers 
 * such as spell check and proper name If the initial query resulted in zero 
 * hits and the search parameter resubmitflags was set when searching, the query 
 * result will contain two query transformations. The query transformations 
 * feedback for the initial query performed is returned by calling 
 * {@link getQueryTransformations}(false). The feedback for the resubmitted query 
 * is retreived by calling {@link getQueryTransformations}(true). If the initial 
 * query results in hits or resubmitflags is not set, only one query 
 * transformations will be returned. Retreived by calling 
 * {@link getQueryTransformations}(false)
 * 
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: IQueryResult.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface IQueryResult extends IResultObject {
    /**
     * Returns a list iterator for the document summaries in this query result.
     * 
     * @return Iterator iterator of document summaries in this query result.
     */
    public function documents();

    /**
     * Returns the original query.
     * 
     * @return IQuery
     */
    public function getOriginalQuery();
        
    /**
     * Returns the number of hits/documents this query resulted in.
     * 
     * @return int
     */
    public function getDocCount();
    
    /**
     * Returns the document summary at the given position.
     * 
     * @param int $index 
     * @return IDocumentSummary
     */
    public function getDocument($index);  

    /**
     * An estimated value indicating the maximum possible ranking value for the 
     * current search.
     * 
     * @return float
     */
    public function getMaxRank();

    /**
     * Returns the time used by the search engine to perform the query.
     * 
     * @return float
     */
    public function getTimeUsed();
    
    /**
     * Returns the name of the search segment from which these query results came.
     * 
     * @return string 
     */
    public function getSegmentName();
    
    /**
     * Returns the query transformations in form of an {@link IQueryTransformations}
     * instance. 
     * @param boolean $resubmitted
     */
    public function getQueryTransformations($resubmitted=false);

    /**
     * Returns the number of navigators.
     * 
     * @return int
     */
    public function navigatorCount();
    
    /**
     * Returns an iterator for the navigators in this query result.
     * 
     * @return Iterator
     */
    public function navigators();
    
    /**
     * Returns the http search request url in case of the use 
     * of a http search engine. implemented for debugging reasons.
     * 
     * @return string 
     */
    public function getHttpSearchURL();
    
    /**
     * Returns a string representing the query result.
     * 
     * @return string
     */
    public function __toString();
}
