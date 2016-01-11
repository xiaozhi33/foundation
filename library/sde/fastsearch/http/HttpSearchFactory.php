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
require_once dirname(__FILE__).'/../ISearchFactory.php';
require_once dirname(__FILE__).'/HttpSearchEngine.php';

// TODO add support for multiple qservers. only the first qserver is used at the moment. 
 
/**
 * @package sde_fastsearch_http
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: HttpSearchFactory.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class HttpSearchFactory implements ISearchFactory {

    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_qservers   = array();
    private $_engine     = null;
    private $_properties = array(); 
    
    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    public function setQservers($qservers) {
        $this->_qservers = explode(",",$qservers);
        for ($i=0; $i<count($this->_qservers); $i++) {
            $this->_qservers[$i] = trim($this->_qservers[$i]);
        }
    }
    
    public function getSearchView($name='default', $reload=false) {
        // TODO implement reload
       $this->initEngine();
       return $this->_engine->getSearchView($name);
    }
    
    public function getSearchViewList() {
        $this->initEngine();
        return $this->_engine->getSearchViewList();        
    }

    public function setSearchviewList(array $viewList) {
        $this->initEngine();
        $this->_engine->setSearchViewList($viewList);
    }
    
    public function setProperties(array $properties) {
        $this->_properties = $properties;            
    }
    
    //--------------------------------------------------------------------------
    //---                         private methods                            ---
    //-------------------------------------------------------------------------- 
    /**
     * @throws SearchEngineException
     */
    private function initEngine() {
        if (!$this->_qservers) {
            throw new SearchEngineException("Cannot init search engine. "
                ."No query servers available. "
                ."Call HttpSearchFactory::setQservers() first.");
        }
        
        if (!$this->_engine) {
            $this->_engine = new HttpSearchEngine($this->_properties);
        }
        $this->_engine->setServerAddress($this->_qservers[0]);
    }
}
