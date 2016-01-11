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

// TODO implement different ModifyModes as instances of ModifyMode (static block?)

/**
 * Contains modification modes deciding how to modify a query based on an 
 * {@link IModifier} and an {@link INavigator}.
 * 
 * @package sde_fastsearch_navigation
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: ModifyMode.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class ModifyMode {
    
    //--------------------------------------------------------------------------
    //---                         constants                                  ---
    //-------------------------------------------------------------------------- 
    /**
     * Unknown modifer.
     */
    CONST MODE_UNKNOWN = 0;
    
    /**
     * Exclude modifier's interval only.
     */
    CONST MODE_EXCLUDE = 1;
    
    /**
     * Exclude data in and "above" modifier's interval. 
     */
    CONST MODE_EXCLUDE_ABOVE = 2;
          
    /**
     * Exclude data in and "below" modifier's interval. 
     */
    CONST MODE_BELOW = 3;
          
    /**
     * Include modifier's interval only.
     */
    CONST MODE_INCLUDE = 4;

    /**
     * Include data in and "above" modifier's interval. 
     */
    CONST MODE_INCLUDE_ABOVE = 5;
          
    /**
     * Include data in and "below" modifier's interval.
     */
    CONST MODE_INCLUDE_BELOW = 6;
    
    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    /**
     * An unmodifiable list of values.
     */
    private static $_validModes = array(1,2,3,4,5,6);
    
    /**
     * An unmodifiable mapping of values to modify strings.
     */
    private static $_modeStrings = 
        array(0 => "unknown", 1=>"-",2=>"",3=>"",4=>"+",5=>"",6=>"");

    //--------------------------------------------------------------------------
    //---                         constructor                                ---
    //-------------------------------------------------------------------------- 
    /**
     * Constructor.
     */
    private function __construct() {}
        
    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    /**
     * Checks if the given int value representing a modify mode is a valid one. 
     *
     * @param int $mode
     * @return boolean
     */
    public static function isValidMode($mode) {
        return in_array($mode, ModifyMode::$_validModes);     
    }
    
    /**
     * Returns a string representing the given mode.
     *
     * @param int $mode
     * @return string
     */
    public static function getModeString($mode) {
        if (ModifyMode::isValidMode($mode)) {
            return ModifyMode::$_modeStrings[$mode];
        } else {
            return ModifyMode::$_modeStrings[ModifyMode::MODE_UNKNOWN];
        }
    }
    
}
