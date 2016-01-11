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
require_once dirname(__FILE__).'/ConfigurationException.php';

// TODO discuss if singleten pattern is useful here.

/**
 * Factory to create instances of {@link ISearchFactory} implementations. 
 *
 * @package sde_fastsearch
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: SearchFactory.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
final class SearchFactory {
    
    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    /**
     * {@link ISearchFactory} instances.
     */
    private static $_searchFactories = array();
    
    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    /**
     * Createas and returns a new instance of an implementation of the interface 
     * {@link ISearchFactory} and throws a {@link ConfigurationException} in case of misconfiguration.
     * If this method was called with the same valid 'fastsearch.SearchFactory'
     * property before, the former created {@link ISearchFactory} instance
     * will be returned. If no 'fastsearch.SearchFactory' property is given a 
     * http search factory will be returned. 
     * 
     * Example:
     * <code>
     * $properties = array(
     *     'fastsearch.SearchFactory' => 'http.HttpSearchFactory',
     *     'fastsearch.http.qservers' => 'myhostname:12345'
     *     );
     * $factory = SearchFactory::newInstance($properties)
     * </code>
     * 
     * @param array $properties Array containing properties
     * @return ISearchFactory 
     * @throws {@link ConfigurationException}
     */
    public static function newInstance(array $properties=array()) { 
        // default properties
        $defaultFactory      = 'http.HttpSearchFactory';
        $defaultHttpQservers = 'localhost:12345'; 
        
        
        if (!is_array($properties) || empty($properties['fastsearch.SearchFactory'])) {
            $properties['fastsearch.SearchFactory'] = $defaultFactory;
        } 
        if ($properties['fastsearch.SearchFactory'] == $defaultFactory 
            && (!is_array($properties) || empty($properties['fastsearch.http.qservers']))) {
            $properties['fastsearch.http.qservers'] = $defaultHttpQservers;
        }        
        
        // create factory instance 
        $filename = 
            str_replace('.', '/', $properties['fastsearch.SearchFactory']).'.php';
        if (!include_once($filename)) {
           throw new ConfigurationException(
                "Cannot find class '".$properties['fastsearch.SearchFactory']."'." 
                ." Please check value of property 'fastsearch.SearchFactory'"
                ); 
        }
        
        $newClassName = substr($properties['fastsearch.SearchFactory'],
            strrpos($properties['fastsearch.SearchFactory'],'.')+1);
  
        $newInstance  = new $newClassName();
        $newInstance->setProperties($properties);
        if ($properties['fastsearch.SearchFactory']==$defaultFactory) {
            $newInstance->setQservers($properties['fastsearch.http.qservers']);
        }
        return $newInstance;
    }
}
