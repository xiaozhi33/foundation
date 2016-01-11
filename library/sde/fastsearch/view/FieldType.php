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

// TODO implement different BaseParameters as instances of BaseParameter (static block?)

/**
 * Contains available field types.
 * 
 * @package sde_fastsearch_view
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: FieldType.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class FieldType {

    //--------------------------------------------------------------------------
    //---                         constants                                  ---
    //-------------------------------------------------------------------------- 
    CONST UNKNOWN    = 0;
    CONST STRING     = 1;
    CONST TEXT       = 2;
    CONST INTEGER    = 3;
    CONST DOUBLE     = 4;
    CONST FLOAT      = 5;
    
    CONST DATETIME   = 6;
    CONST SCOPE      = 7;
    CONST FIELDGROUP = 8;
    
    //--------------------------------------------------------------------------
    //---                      private fields                                ---
    //-------------------------------------------------------------------------- 
    private static $_validTypes = array(0,1,2,3,4,5,6,7,8);
        
    //--------------------------------------------------------------------------
    //---                         constructor                                ---
    //-------------------------------------------------------------------------- 
    private function __construct() {}

    //--------------------------------------------------------------------------
    //---                        public methods                              ---
    //-------------------------------------------------------------------------- 
    /**
     * Checks if given int value representing a field type is a valid one.
     * @param int $fieldType
     * @return boolean
     */
    public static function isValidType($fieldType) {
        return array_key_exists($fieldType, FieldType::$_validTypes);
    } 
}
