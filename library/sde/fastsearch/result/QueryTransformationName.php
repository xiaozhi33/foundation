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
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: QueryTransformationName.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class QueryTransformationName {
    
    //--------------------------------------------------------------------------
    //---                         public fields                             ---
    //-------------------------------------------------------------------------- 
    /**
     * Name of query transformer DidYouMean.
     */
    CONST DID_YOU_MEAN = "FastQT_DidYouMean";

    /**
     * Name of query transformer Acronym.
     */
    CONST ACRONYM = "FastQT_Acronym";
    
    /**
     * Name of query transformer Lemmatizer/DefaultIndex.
     */
    CONST LEMMATIZE = "FastQT_DefaultIndex";
    
    /**
     * Name of anti-phrasing query transformer This is used to refer to this 
     * transformation type.
     */
    CONST NON_PHRASE = "FastQT_NonPhrase";
    
    /**
     * Name of query transformer FixedPoint.
     */
    CONST FIXED_POINT = "FastQT_FixedPoint";
    
    /**
     * Name of query transformer ProperName This is used to refer to this 
     * transformation type.
     */
    CONST PROPERNAME = "FastQT_ProperName";
    
    /**
     * Name of the second proper name query transformer This is used to refer to 
     * this transformation type.
     */
    CONST PROPERNAME2 = "FastQT_ProperName2";
    
    /**
     * Name of the third proper name query transformer This is used to refer to 
     * this transformation type.
     */
    CONST PROPERNAME3 = "FastQT_ProperName3";
    
    /**
     * Name of the fourth proper name query transformer This is used to refer to 
     * this transformation type.
     */
    CONST PROPERNAME4 = "FastQT_ProperName4";
    
    /**
     * Name of the fift proper name query transformer This is used to refer 
     * to this transformation type.
     */
    CONST PROPERNAME5 = "FastQT_ProperName5";
    
    /**
     * Name of query transformer ProtectQuery This is used to refer to this 
     * transformation type.
     */
    CONST PROTECT_QUERY = "FastQT_ProtectQuery";
    
    /**
     * Name of query transformer QueryBoost This is used to refer to this 
     * transformation type.
     */
    CONST QUERY_BOOSTING = "FastQT_QueryBoosting";
    
    /**
     * Name of query transformer Similar.
     * 
     */
    CONST SIMILAR = "FastQT_Similar";
    
    /**
     * Name of query transformer SpellCheck This is used to refer to this 
     * transformation type.
     */
    CONST SPELLCHECK = "FastQT_SpellCheck";
    
    /**
     * Name of query transformer UTF8.
     */
    CONST UTF8 = "FastQT_UTF8";
    
    /**
     *  Name of query transformer DowncaseUTF8.
     */
    CONST DOWNCASE_UTF8 = "FastQT_DowncaseUTF8";
    
    /**
     * Name of query transformer GuessLang Try to guess the natural language 
     * of the query, if not given.
     */
    CONST GUESS_LANG = "FastQT_GuessLang";
    
    /**
     * Name of query transformer DetectUrl.
     */
    CONST DETECT_URL = "FastQT_DetectURL";
    
    /**
     * Name of query transformer Resubmit This is used to refer to this 
     * transformation type.
     */
    CONST RESUBMIT = "FastQT_ResubmitQuery";
    
    /**
     * Name of query transformer Navigation.
     */
    CONST NAVIGATION = "FastQT_Navigation";
}
