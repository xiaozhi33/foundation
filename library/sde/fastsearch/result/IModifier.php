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
 * Represents exactly one interval in exactly one navigator. Can be used with 
 * {@link INavigator} to represent a drill-down path and create a corresponding 
 * query.
 * 
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: IModifier.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface IModifier extends IResultObject {

    /**
     * Returns the print name of this modifier.
     * @return stirng 
     */
    public function getName();
    
    /**
     * Updates the print name of this modifier.
     * @param string $name
     */
    public function setName($name);
    
    /**
     * Returns the number of documents in this interval. 
     * @return int
     */
    public function getCount();
    
    /**
     * Returns the internal value of this modifier.
     * @return string
     */
    public function getValue();

    /**
     * Returns the ratio of this modifier's document count to the total document 
     * count.
     * @return flaot
     */
    public function getDocumentRatio();

    /**
     * Returns the name of the attribute affected by the modifier.
     * @return string
     */
    public function getAttribute();
          
    /**
     * Returns the enclosing navigator.
     * @return INavigator
     */
    public function getNavigator();

    /**
     * Sets the enclosing navigator.
     * @param INavigator
     */
    public function attach(INavigator $navigator);
    
    /**
     * Returns a new modifier identical to this modifier, but removed from 
     * its INavigator.
     * @return IModifier
     */
    public function detach();    

    /**
     * Checks whether this modifer is detached.
     * 
     * @return boolean
     */
    public function isDetached();

    /**
     * Returns whether this modifier represents a range interval in the form 
     * [from;to] or not.
     * @return boolean
     */
    public function isInterval();
}
