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
 * Represents one query transformation performed on the 
 * submitted query. The query transformation can be a modification of the 
 * original query or just a suggestion on how to improve the quality of the 
 * query.
 * 
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: IQueryTransformation.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface IQueryTransformation extends IResultObject {
    /**
     * Returns the action of this query transformation.
     * @return string 
     */
    public function getAction();
    
    /**
     * Returns the message of this query transformation.
     * @return string
     */
    public function getMessage();

    /**
     * Returns the custom field of this query transformation.
     * @return string
     */
    public function getCustom();
         
    /**
     * Returns the message id for the message in this query transformation.
     * @return int
     */
    public function getMessageID();

    /**
     * Returns the name of this query transformation.
     * @return string
     */
    public function getName();

    /**
     * Returns the query after the transformation.
     * @return string
     */
    public function getQuery();
}
