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
 * Represents a document summary in a {@link IQueryResult}. 
 * The document summary contains a set of fields defined in the configuration 
 * of the search engine returning the results.
 * 
 * @package sde_fastsearch_result
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: IDocumentSummary.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
interface IDocumentSummary extends IResultObject {
    /**
     * Returns the number of fields in this document summary.
     * 
     * @return int
     */
    public function fieldCount();
    
    /**
     * Returns the document number/index of this document.
     * 
     * @return int
     */
    public function getDocNo();
    
    /**
     * Returns the summary field at given index {@link $index}.
     * 
     * @param  int $index
     * @return IDocumentSummaryField
     */
    public function getSummaryField($index);
    
    /**
     * Returns the summary field with given name {@link $name}.
     * 
     * @param  string $name
     * @return IDocumentSummaryField
     */
    public function getSummaryFieldByName($name);

    /**
     * Adds an {@link IDocuemntSummaryField}. If a field 
     * was with the same name was set before, it will be overridden. 
     * 
     * @param  IDocuemntSummaryField $field
     */
    public function setSummaryField(IDocumentSummaryField $field);
    
    /**
     * Returns an iterator for the summary fields in this document summary.
     * @return Iterator
     */
    public function summaryFields(); 
}
