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
require_once dirname(__FILE__).'/INavigator.php';

/**
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: Navigator.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class Navigator implements INavigator {

    //--------------------------------------------------------------------------
    //---                          private fields                            ---
    //-------------------------------------------------------------------------- 
    private $_queryResult = null;
    private $_name = "";
    private $_fieldName = "";
    private $_displayName = "";
    private $_type = 0;
    private $_unit = "";
    private $_score = 0.0;
    private $_hits = 0;
    private $_hitsUsed = 0;
    private $_hitRatio = 0.0;
    private $_sampleCount = 0;
    private $_min = 0.0;
    private $_max = 0.0;
    private $_mean = 0.0;
    private $_frequencyError = 0;
    private $_entropy = 0.0;
    
    private $_modifiers       = null;
    private $_modifierNames   = null;

    //--------------------------------------------------------------------------
    //---                          constructor                               ---
    //-------------------------------------------------------------------------- 
    /**
     * Constructor.
     * @param string $name
     * @param string $fieldName
     * @param string $displayName
     * @param int $type
     * @param string $unit
     * @param float $score;
     * @param int $hits;
     * @param int $hitsUsed
     * @param float $hitRatio
     * @param int $sampleCount
     * @param float $min
     * @param float $max
     * @param float $mean
     * @param int $frequencyError
     * @param float $entropy
     * @param array $modifiers
     */
    public function __construct(
        $name,
        $fieldName,
        $displayName, 
        $type,
        $unit,
        $score,
        $hits,
        $hitsUsed,
        $hitRatio,
        $sampleCount,
        $min,
        $max,
        $mean,
        $frequencyError,
        $entropy,
        array $modifiers) {
        
        $this->_name = (string)$name;
        $this->_fieldName = (string)$fieldName;
        $this->_displayName = (string)$displayName;
        $this->_type = (int)$type;
        $this->_unit = (string)$unit;
        $this->_score = (float)$score;
        $this->_hits = (int)$hits;
        $this->_hitsUsed = (int)$hitsUsed;
        $this->_hitRatio = (float)$hitRatio;
        $this->_sampleCount = (int)$sampleCount;
        $this->_min = (float)$min;
        $this->_max = (float)$max;
        $this->_mean = (float)($mean);
        $this->_frequencyError = (int)$frequencyError;
        $this->_entropy = (float)$entropy;

        $this->_modifierNames = new ArrayObject();
        $this->_modifiers = new ArrayObject();
        
        foreach ($modifiers as $modifier) {
            if ($modifier instanceof IModifier) {
                $modifier->attach($this);
                $this->_modifiers->offsetSet($modifier->getName(), $modifier);
                $this->_modifierNames->append($modifier->getName());                     
            } 
        }
    }
    
    //--------------------------------------------------------------------------
    //---                          public methods                            ---
    //-------------------------------------------------------------------------- 
    public function getName() {
        return (string)$this->_name;
    }
    
    public function getFieldName() {
        return (string)$this->_fieldName;
    }

    public function getDisplayName() {
        return $this->_displayName;
    }
    
    public function getType() {
        return $this->_type;
    }
    
    public function getUnit() {
        return $this->_unit;
    }

    public function getScore() {
        return $this->_score;
    }
    
    public function getHits() {
        return $this->_hits;
    }
          
    public function getHitsUsed() {
        return $this->_hitsUsed;
    }
    
    public function getHitRatio() {
        return $this->_hitRatio;
    }

    public function getSampleCount() {
        return $this->_sampleCount;
    }

    public function getMin() {
        return $this->_min;
    }   
    
    public function getMax() {
        return $this->_max;
    }
    
    public function getMean() {
        return $this->_mean;
    }
    
    public function getFrequencyError() {
        return $this->_frequencyError;
    }    
    
    public function getEntropy() {
        return $this->_entropy;
    }

    public function modifierNames() {
        return $this->_modifierNames->getIterator();
    }        
    
    public function modifiers() {
        return $this->_modifiers->getIterator();
    }

    public function modifierCount() {
        return $this->_modifiers->count();
    }    
    
    public function getModifier($name) {
        return $this->_modifiers->offsetGet($name);
    }
     
    public function attach(IQueryResult $queryResult) {
        // TODO do real attach here -> add navigator to query?
        $this->_queryResult = $queryResult;    
    }
    
    public function detach() {
        // TODO do real detach here -> remove original navigator (this) from query
        return new Navigator(
            null, 
            $this->_name,
            $this->_fieldName,
            $this->_displayName, 
            $this->_type,
            $this->_unit,
            $this->_score,
            $this->_hits,
            $this->_hitsUsed,
            $this->_hitRatio,
            $this->_sampleCount,
            $this->_min,
            $this->_max,
            $this->_mean,
            $this->_frequencyError,
            $this->_entropy,
            $this->_modifiers->getArrayCopy());
    }
    
    public function isDetached() {
        if (!$this->_queryResult) {
            return true;
        } else {
            return false;
        }
    }
    
    public function prepareUnset() {
               
        // remove modifier references 
        $modIter = new ArrayIterator();
        $modIter = $this->_modifiers->getIterator();
        while ($modIter->valid()) {
            $modifier = $modIter->current();
            $modifier->prepareUnset();
            $modIter->next();
        }
        
        // remove other references
        $this->_modifiers     = null;
        $this->_queryResult   = null;
        $this->_modifierNames = null;
    }
    
}
