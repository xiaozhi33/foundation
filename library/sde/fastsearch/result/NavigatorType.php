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

/**
 * Contains available navigator types.
 * 
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: NavigatorType.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class NavigatorType {
    
    //--------------------------------------------------------------------------
    //---                           constants                                ---
    //-------------------------------------------------------------------------- 
    /**
     * DATETIME navigator type
     */
    CONST DATETIME = 1;

    /**
     * DISCRETENUMERIC navigator type
     */
    CONST DISCRETENUMERIC = 2;

    /**
     * DOUBLE navigator type
     */
    CONST DOUBLE = 3;

    /**
     * INTEGER navigator type
     */
    CONST INTEGER = 4;

    /**
     * STRING navigator type
     */
    CONST STRING = 5;

    //--------------------------------------------------------------------------
    //---                           private fields                           ---
    //-------------------------------------------------------------------------- 
    /**
     * An unmodifiable list of all valid navigator types
     */
    private static $_validTypes = array(1,2,3,4,5);

    //--------------------------------------------------------------------------
    //---                         constructor                                ---
    //-------------------------------------------------------------------------- 
    /**
     * Private constructor.
     */
    private function __construct() {}
    
    //--------------------------------------------------------------------------
    //---                           public methods                           ---
    //-------------------------------------------------------------------------- 
    /**
     * Checks if given int value representing a navigator type is a valid one.
     * @param int $type
     * @return boolean
     */
    public static function isValidType($type) {
        return array_key_exists($type, NavigatorType::$_validTypes);
    } 
}
