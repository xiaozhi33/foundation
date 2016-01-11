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
require_once dirname(__FILE__).'/IQuery.php';
require_once dirname(__FILE__).'/BaseParameter.php';
require_once dirname(__FILE__).'/SearchParameter.php';
require_once dirname(__FILE__).'/ParameterException.php';
require_once dirname(__FILE__).'/NoSuchParameterException.php';

/**
 * @package sde_fastsearch_query
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: Query.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class Query implements IQuery {
    
    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_parameters  = array();
    
    
    //--------------------------------------------------------------------------
    //---                         constructor                                ---
    //-------------------------------------------------------------------------- 
    public function __construct(IQuery $query=null) {
        if ($query != null) {
            $this->_parameters = $query->getSearchParameters();
        }
    }
    
    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    public function setQueryString($queryString) {
        $this->setParameterByType(BaseParameter::QUERY, $queryString);
    }
    
    public function getQueryString() {
        $queryKey = BaseParameter::getParameterString(BaseParameter::QUERY);
        if (!isset($this->_parameters[$queryKey]))
            return "";
        else 
            return $this->_parameters[$queryKey]->getStringValue();
    }

    public function setParameter(SearchParameter $param) {
        if ($param != null) {
            $this->_parameters[$param->getName()] = $param;
        } else {
            throw new ParameterException("Given Parameter is 'null'.");
        }
    }

    public function setParameterByType($paramType, $value) {
        if (!BaseParameter::isValidParameter($paramType)) {
            throw new ParameterException("Given type is not a valid parameter type");
        }
        
        $param = new SearchParameter($paramType, $value);
        $this->_parameters[$param->getName()] = $param;
    }

    public function setParameterByName($paramName, $value) {
        $param = new SearchParameter(BaseParameter::UNKNOWN, $value, 
            $paramName);
        $this->_parameters[$param->getName()] = $param;    
    }
    
    public function getSearchParameters() {
        return $this->_parameters;
    }
    
    public function getParameterByType($paramType) {
        if(isset($this->_parameters[BaseParameter::getParameterString($paramType)])) {
            return $this->_parameters[BaseParameter::getParameterString($paramType)];
        } else {
            throw new NoSuchParameterException("Parameter '"
                .BaseParameter::getParameterString($paramType)."' not set");
        }
    }
    
    public function getParameterByName($paramName) {
        if(isset($this->_parameters[$paramName])) {
            return $this->_parameters[$paramName];
        } else {
            throw new NoSuchParameterException("Parameter '$paramName' not set");
        }
    }
    
    public function addFilterTerm($term) {
        $filterStr = "filter(".$term.")";
        $filterKey = BaseParameter::getParameterString(BaseParameter::FILTER);
        if (isset($this->_parameters[$filterKey])) {
            $value = $this->_parameters[$filterKey]->getValue();
            $value .= ", $filterStr";
        }
    }
}
