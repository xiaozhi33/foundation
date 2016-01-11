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
require_once dirname(__FILE__).'/ISearchView.php';

/**
 * @package sde_fastsearch_view
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de> 
 * @version $Id: SearchView.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class SearchView implements ISearchView {

    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_name = "";
    private $_description = ""; 
    private $_created = 0;
    private $_lastSaved = 0;
    private $_engine = null;

    //--------------------------------------------------------------------------
    //---                           constructor                              ---
    //-------------------------------------------------------------------------- 
    public function __construct($name) {
        $this->_name = $name;
    }

    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    public function getName() {
        return $this->_name;
    }
    
    public function getDescription() {
        return $this->_description;
    }

    public function setDescription($description) {
        $this->_description = $descripton;
    }
    
    public function getCreated() {
        return $this->_created;
    }
    
    public function setCreated($created) {
        $this->_created = $created;
    }
    
    public function getLastSaved() {
        return $this->_lastSaved;
    }
    
    public function setLastSaved($lastSaved) {
        $this->_lastSaved = $lastSaved;
    }
    
    public function initEngine(ISearchEngine $engine) {
        $this->_engine = $engine;
    }
    
    public function search(IQuery $query) {
        if (!$this->_engine) {
           throw new SearchEngineException("No SearchEngine available. "
               ."Call SearchView::initEngine() first.");  
        }
        // set view for query
        if ($this->getName() != 'default') {
            $query->setParameterByType(BaseParameter::VIEW,$this->getName());
        }
        return $this->_engine->search($query);        
    }
}
