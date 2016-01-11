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
require_once dirname(__FILE__).'/IQueryTransformations.php';

/**
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id $
 */
class QueryTransformations implements IQueryTransformations {

    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_modAction  = "modified";
    private $_suggAction = "suggested";
    
    private $_queryTransformations = array();

    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    /**
     * Constructor.
     * @param array
     * @return QueryTransformations
     */
    public function __construct(array $queryTransformations) {
        foreach ($queryTransformations as $transformation) {
            
            if ($transformation instanceOf IQueryTransformation) {
                $this->_queryTransformations[] = $transformation;         
            }
        }
    }

    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    public function getAllQueryTransformations() {
        return new ArrayObject($this->_queryTransformations);
    }
    
    public function getTransformation($name) {
        return $this->findQTF($name);
    }
    
    public function getTransformationsByName($name) {
         $transformations = array();
         foreach ($this->_queryTransformations as $transformation) {
             if ($transformation->getName() == $name) {
                 $modifications[] = $transformation;
             }
         }
         return new ArrayObject($modifications);
    }
    
    public function getModification($name) {
        return $this->findQTF($name, $this->_modAction);
    }

    public function getModifications() {
         $modifications = array();
         foreach ($this->_queryTransformations as $transformation) {
             if (ereg("^".$this->_modAction,strtolower($transformation->getAction()))) {
                 $modifications[] = $transformation;
             }
         }
         return new ArrayObject($modifications);
    }
              
    public function getModificationsByName($name) {
         $modifications = array();
         foreach ($this->_queryTransformations as $transformation) {
             if (ereg("^".$this->_modAction,strtolower($transformation->getAction()))
                && $transformation->getName() == $name) {
                 $modifications[] = $transformation;
             }
         }
         return new ArrayObject($modifications);
    }

    public function  getSubmittedQuery() {
         // TODO implement method getSubmittedQuery();
         return "";           
    }
              
    public function getSuggestion($name) {
         return $this->findQTF($name, $this->_suggAction);
    }
     
    public function getSuggestions() {
         $suggestions = array();
         foreach ($this->_queryTransformations as $transformation) {
             if (ereg("^".$this->_suggAction,strtolower($transformation->getAction()))) {
                 $suggestions[] = $transformation;
             }
         }
         return new ArrayObject($suggestions);
    }
     
    public function getSuggestionsByName($name) {
         $suggestions = array();
         foreach ($this->_queryTransformations as $transformation) {
            if (ereg("^".$this->_suggAction,strtolower($transformation->getAction()))
                && $transformation->getName() == $name) {
                $suggestions[] = $transformation;
            }
         }
         return new ArrayObject($suggestions);
     }
     
    public function hasTransformation($name) {
        return $this->getTransformation($name) != null;
    }
    
    public function hasModification($name) {
        return $this->getModification($name) != null;
    }
          
    public function hasSuggestion($name) {
        return $this->getSuggestion($name) != null;
    }
    
    public function prepareUnset() {
        foreach($this->_queryTransformations as $qtf) {
            $qtf->prepareUnset();
        }
        $this->_queryTransformations = null;
    }

    //--------------------------------------------------------------------------
    //---                         private methods                            ---
    //-------------------------------------------------------------------------- 
    private function findQTF($name, $action="") {
        foreach($this->_queryTransformations as $transformation) {
            if ($transformation->getName() == $name) {
                if (empty($action) 
                    || ereg("^".strtolower($action),strtolower($transformation->getAction()))) {
                    return $transformation;
                }
            }
        }
        return null;
    }
}
