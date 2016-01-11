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
 * Represents one field in an {@link IDocumentSummary}. 
 * The field is identified by a name and a summary string.
 * 
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: IDocumentSummaryField.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface IDocumentSummaryField extends IResultObject {
    
    /**
     * Returns the value of this summary field.
     * 
     * @return mixed
     */
    public function getValue();
    
    /**
     * Returns the name of this summary field.
     * 
     * @return string
     */
    public function getName();
    
    /**
     * Returns string value of this summary field.
     * 
     * @return string
     */
    public function getStringValue();
    
    /**
     * Returns the type of this summary field. Valid types are defined in 
     * {@link FieldType}.
     * 
     * @return int
     */
    public function getType();
    
    /**
     * Returns true if this summary field is empty.
     * 
     * @return boolean
     */
    public function isEmpty();
}
