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
 * Represents a fixpoint Live Analytics/ Dynamic Drill Down in in 
 * structured content. Represents exactly one structured 
 * "aspect" of a query result, eg. "size of document", "author rating" or a 
 * discrete element such as "author's academic degree". The intervals, or 
 * values, for such an aspect are represented by {@link IModifier}s.
 * 
 * A navigator and its modifiers may used, along with the {@link INavigation} 
 * interface, to modify the original query for navigation in a document base.
 * 
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: INavigator.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface INavigator extends IResultObject {
    /**
     * Returns a human readable name for this navigiator.
     * @return string
     */
    public function getDisplayName();
          
    /**
     * Returns the entropy of this navigator, as reported by Search Engine.
     * @return float
     */
    public function getEntropy();
          
    /**
     * Returns the name of the mod-navigator field.
     * @return string
     */
    public function getFieldName();
          
    /**
     * Returns the frequency error number.
     * 
     * @return int
     */
    public function getFrequencyError();
    
    /**
     * Returns the hit ratio of this navigator, as reported by Search Engine.
     * @return float
     */
    public function getHitRatio();
    
    /**
     * Returns the hits count of this navigator, as reported by Search Engine.
     * @return int
     */
    public function getHits();
          
    /**
     * Returns the hits-used count of this navigator, as reported by Search Engine.
     * @return int
     */ 
    public function getHitsUsed();
          
    /**
     * Returns the maximum value of this navigator, as reported by Search Engine.
     * @return float
     */
    public function getMax();
          
    
    /**
     * Returns the mean value of this navigator, as reported by Search Engine.
     * @return float
     */
    public function getMean();
          
    /**
     * Returns the minimum value of this navigator, as reported by Search Engine.
     * @return float 
     */
    public function getMin();
          
    /**
     * Returns the {@link IModifier} with given name {@link $name}.
     * @param string $name
     * @return IModifier 
     */
    public function getModifier($name);
    
    /**
     * Returns the formal name of this navigator, and the "aspect" it models.
     * @return string
     */
    public function getName();
    
    /**
     * Returns the sample count of this navigator, as reported by Search Engine.
     * @return int
     */
    public function getSampleCount();
          
    /**
     * Returns the score of this navigator, as reported by Search Engine.
     * @return float
     */
    public function getScore();
          
    /**
     * Returns the type of this navigator. Valid types are defined in 
     * {@link NavigatorType}.
     * @return int
     */
    public function getType();
          
    /**
     * Returns the unit of this navigator.
     * @return string
     */
    public function getUnit();

    /**
     * Attaches this navigator to the given {@link IQueryResult}
     * @param IQueryResult $queryResult
     */
    public function attach(IQueryResult $queryResult);
    
    /**
     * Returns a new navigator identical to this navigator, but removed from 
     * its {@link IQueryResult}.
     * @return INavigator 
     */
    public function detach();
    
    /**
     * Checks whether this navigator is detached.
     * @return boolean
     */
    public function isDetached();
          
    /**
     * Returns the number of modifiers.
     * @return int
     */
    public function modifierCount();
          
    /**
     * Iterates over names of the modifiers.
     * @return Iterator
     */
    public function modifierNames();
          
    /**
     * Iterates over the modifiers.
     * @return Iterator
     */
    public function modifiers();
}
