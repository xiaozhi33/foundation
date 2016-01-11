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
 * Contains default supported search parameters.
 * 
 * @package sde_fastsearch_query
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: BaseParameter.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class BaseParameter {

    //--------------------------------------------------------------------------
    //---                         constants                                  ---
    //-------------------------------------------------------------------------- 
    CONST UNKNOWN                  = 0;
    CONST QUERY                    = 1;
    CONST HITS                     = 2;
    CONST OFFSET                   = 3;
    CONST FILTER                   = 4;
    CONST RESULT_VIEW              = 5;
    CONST NAVIGATION               = 6;
    CONST NAVIGATORS               = 7;
    CONST NAVIGATION_RESULTVIEW    = 8;
    CONST NAVIGATION_HITS          = 9;
    CONST NAVIGATION_FILTER        = 10;
    CONST SPELL                    = 11;
    CONST VIEW                     = 12;
    CONST SORT_BY                  = 13;
    CONST LEMMATIZE                = 14;
    CONST LEMMATIZER_DECOMPOUNDING = 19;
    CONST RESUBMITFLAGS            = 15;
    CONST SIMILAR_TYPE             = 16;
    CONST SIMILAR_TO               = 17;
    CONST SORTSIMILAR              = 18;
    
    /* available types in java api 
    CONST BASE_TYPE_NAVIGATION_DEEPHITS   = 10;
    CONST QR_ESC_NEWL = 43;
    CONST ENCODING = 6;
    CONST LANGUAGE = 7;
    CONST COLLAPSING = 9;
    
    CONST DUPLICATIONREMOVAL = 12;
    CONST DUPREM_SLOT1 = 13;
    CONST DUPREM_SLOT2 = 14;
    CONST CLUSTERING = 15;
    CONST CLU_SORTING = 16;
    CONST CLU_THRESHOLD = 17;
    CONST CLU_SIZE = 18;
    CONST CLU_EQUALITY = 19;
    CONST CLU_LABELS = 20;
    CONST CLU_TREESIZE = 21;
    CONST QTF_SECURITY_ENABLE = 22;
    CONST QTF_SECURITY_UID = 23;
    CONST RPF_SECURITY_ENABLE = 24;
    CONST RPF_SECURITY_UID = 25;
    CONST PROXIMITYBOOST = 26;
    CONST PROXIMITYBOOST_HITS = 27;
    CONST PROXIMITYBOOST_PARAMS = 28;
    CONST DATETIME = 39;
    CONST TRIGGERID = 40;
    CONST DELETETRIGGER = 41;
    CONST ASCENDING = "";
    CONST DESCENDING = "";
    */    

    //--------------------------------------------------------------------------
    //---                      private fields                                ---
    //-------------------------------------------------------------------------- 
    private static $_validTypes = array(
        0  => 'unknown',
        1  => 'query',
        2  => 'hits',
        3  => 'offset',
        4  => 'filter',
        5  => 'resultview',
        6  => 'rpf_navigation:enabled',
        7  => 'rpf_navigation:navigators',
        8  => 'rpf_navigation:resultview',
        9  => 'rpf_navigation:hits',
        10 => 'navigation',
        11 => 'spell',
        12 => 'view',
        13 => 'sortby',
        14 => 'qtf_lemmatize',
        19 => 'qtf_lemmatizer:decompounding',
        15 => 'resubmitflags',
        16 => 'similartype',
        17 => 'similarto',
        18 => 'rpf_sortsimilar:enabled'
        );
        
    //--------------------------------------------------------------------------
    //---                         constructor                                ---
    //-------------------------------------------------------------------------- 
    private function __construct() {}

    //--------------------------------------------------------------------------
    //---                        public methods                              ---
    //-------------------------------------------------------------------------- 
    /**
     * Checks if given int value representing a search parameter is a valid one.
     * 
     * @param int $param
     * @return boolean
     */
    public static function isValidParameter($param) {
        return array_key_exists($param, BaseParameter::$_validTypes);
    } 

    /**
     * Returns a string representing the given parameter. 
     * 
     * @param int $param
     * @return string
     */
    public static function getParameterString($param) {
        if (BaseParameter::isValidParameter($param)) {
            return BaseParameter::$_validTypes[$param];
        }
        return BaseParameter::$_validTypes[BaseParameter::UNKNOWN];
    } 
}
