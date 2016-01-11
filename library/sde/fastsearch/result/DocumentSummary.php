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
require_once dirname(__FILE__).'/IDocumentSummary.php';
require_once dirname(__FILE__).'/NoSuchFieldException.php';

/**
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: DocumentSummary.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class DocumentSummary implements IDocumentSummary  {
    
    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_fields = null; 
    private $_docNo  = 0;
    private $_siteId = 0;
    
    //--------------------------------------------------------------------------
    //---                         constructor                                ---
    //-------------------------------------------------------------------------- 
    /**
     * @param int $siteId
     * @param int $docNo
     * @param array $fields Array containing document summary fields. this has 
     *                      to be an associatve array, where the key is equal 
     *                      to the name-property of the document summary field 
     *                      value. Otherwise, the method getSummaryFieldByName
     *                      will not return proper results.
     */
    public function __construct(array $fields, $docNo=0) {
        $this->_fields = new ArrayObject($fields);
        $this->_docNo  = $docNo;
    }
    
    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    public function fieldCount() {
        return $this->_docSums->count();        
    }
    
    public function getDocNo() {
        return $this->_docNo;
    }
    
    public function getSummaryField($index) {
        if (!$this->_fields->offsetExists($index)) {
            throw new NoSuchFieldException("Field with index $index does not exist.");
        } 
        return $this->_fields->offsetGet($index);
    }

    public function getSummaryFieldByName($name) {
        if (!$this->_fields->offsetExists($name)) {
            throw new NoSuchFieldException("Field with name $name does not exist.");
        } 
        return $this->_fields->offsetGet($name);
    }
    
    public function setSummaryField(IDocumentSummaryField $field) {
        $this->_fields->offsetSet($field->getName(), $field);    
    }
    
    public function summaryFields() {
        return $this->_fields->getIterator();
    }

    public function prepareUnset() {
        
        // remove field references
        $fieldIter = $this->_fields->getIterator(); 
        while ($fieldIter->valid()) {
            $field = $fieldIter->current();
            $field->prepareUnset();
            $fieldIter->next();
        }
        $this->_fields = null;
    }
}
