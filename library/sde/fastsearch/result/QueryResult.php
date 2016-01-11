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
require_once dirname(__FILE__).'/IQueryResult.php';

/**
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: QueryResult.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class QueryResult implements IQueryResult  {
    
    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_documents       = null;
    private $_navigators      = null;
    private $_navigatorNames  = null;
    private $_origQuery       = null;  
    private $_transformations = null;
    private $_totalHits       = 0;  
    private $_maxRank         = 0;
    private $_timeUsed        = 0.0;
    private $_segmentName     = "";
    
    // debug data
    private $_httpSearchURL;
    
    //--------------------------------------------------------------------------
    //---                         constructor                                ---
    //-------------------------------------------------------------------------- 
    /**
     * Constructor.
     * @param IQuery $origQuery
     * @param array $documents
     * @param string $segmentName
     * @param int $totalHits
     * @param int $maxRank
     * @param float $timeUsed
     * @param string $httpSearchURL
     * @return QueryResult
     */
    public function __construct(IQuery $origQuery, 
        IQueryTransformations $transformations,
        array $documents, array $navigators, $segmentName, $totalHits, $maxRank, 
        $timeUsed, $httpSearchURL="") {
        $this->_origQuery       = $origQuery;
        $this->_transformations = $transformations;
        $this->_documents       = new ArrayObject($documents);
        $this->_navigators      = new ArrayObject();
        $this->_navigatorNames  = new ArrayObject();
        
        foreach ($navigators as $navigator) {
            if ($navigator instanceof INavigator) {
                $navigator->attach($this);
                $this->_navigators->offsetSet($navigator->getName(), $navigator);
                $this->_navigatorNames->append($navigator->getName());                     
            }
        }
        
        $this->_totalHits    = (int)$totalHits;
        $this->_maxRank      = (int)$maxRank;
        $this->_timeUsed     = (float)$timeUsed;
        $this->_segmentName  = (string)$segmentName;
        $this->_httpSearchURL= $httpSearchURL; 
    }
    
    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    public function documents() {
        return $this->_documents->getIterator();
    }
    
    public function getOriginalQuery() {
        return $this->_origQuery;
    }
        
    public function getDocCount() {
        return $this->_totalHits;
    }
    
    public function getDocument($index) {
        if (!$this->_documents->offsetExists($index)) {
            throw SearchException("Document with index $index does not exist.");
        } 
        return $this->_documents->offsetGet($index);
        
    }

    public function getMaxRank() {
        return $this->_maxRank;
    }

    public function getTimeUsed() {
        return $this->_timeUsed;
    }
    
    public function getSegmentName() {
        return $this->_segmentName;
    }
    
    public function getQueryTransformations($resubmitted=false) {
        return $this->_transformations;
    }
    
    public function navigatorCount() {
        return $this->_navigators->count();
    }
    
    public function navigators() {
        return $this->_navigators->getIterator();
    }
    
    public function getHttpSearchURL() {
        return $this->_httpSearchURL;    
    }
    
    public function __toString() {
        $string  = "segment name : ".$this->getSegmentName()."\n";
        $string .= "total hits   : ".$this->getDocCount()."\n";
        $string .= "returned hits: ".$this->documents()->count()."\n";
        $string .= "max rank     : ".$this->getMaxRank()."\n";
        $string .= "time used    : ".$this->getTimeUsed()."\n";
        
        if($this->documents()->count()>0) {
            $string .= "hit summaries: \n\n";
            $hitIterator = $this->documents();
            while ($docSum = $hitIterator->current()) {
                $string .= "[".$docSum->getDocNo()."] ";
                $string .= 
                    $docSum->getSummaryFieldByName('title')->getStringValue()."\n";
                $hitIterator->next();
            }
        }
        return $string;
    }

    public function prepareUnset() {
        
        // remove document references
        $docIter = new ArrayIterator();
        $docIter = $this->_documents->getIterator();
        while ($docIter->valid()) {
            $document = $docIter->current();
            $document->prepareUnset();
            $docIter->next();
        }
        $this->_documents = null;
        
        // remove navigator references
        $navIter = new ArrayIterator();
        $navIter = $this->_navigators->getIterator();
        while ($navIter->valid()) {
            $navigator = $navIter->current();
            $navigator->prepareUnset();
            $navIter->next();
        }
        $this->_navigators = null;
        
        // remove query transformations reference
        if ($this->_transformations != null) {
            $this->_transformations->prepareUnset();
            $this->_transformations = null;
        }
        
        // remove/unset other references
        $this->_navigatorNames  = null;
        $this->_origQuery       = null;  
    }
}
