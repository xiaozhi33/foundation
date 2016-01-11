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
require_once dirname(__FILE__).'/../ISearchEngine.php';
require_once dirname(__FILE__).'/../SearchEngineException.php';
require_once dirname(__FILE__).'/../view/SearchView.php';
require_once dirname(__FILE__).'/../view/FieldType.php';
require_once dirname(__FILE__).'/../result/QueryResult.php';
require_once dirname(__FILE__).'/../result/DocumentSummary.php';
require_once dirname(__FILE__).'/../result/DocumentSummaryField.php';
require_once dirname(__FILE__).'/../result/Modifier.php';
require_once dirname(__FILE__).'/../result/Navigator.php';
require_once dirname(__FILE__).'/../result/NavigatorType.php';
require_once dirname(__FILE__).'/../result/QueryTransformation.php';
require_once dirname(__FILE__).'/../result/QueryTransformations.php';

/**
 * @package sde_fastsearch_http
 * @author  Andreas Scheerer <Andreas.Scheerer@sueddeutsche.de>
 * @version $Id: HttpSearchEngine.php 11 2009-08-23 04:23:33Z andreas.scheerer $
 */
class HttpSearchEngine implements ISearchEngine {
    
    //--------------------------------------------------------------------------
    //---                         private fields                             ---
    //-------------------------------------------------------------------------- 
    private $_baseURL = "";
    private $_searchViewList = null;  
    private $_properties = array();
    
    //--------------------------------------------------------------------------
    //---                         constructor                                ---
    //-------------------------------------------------------------------------- 
    public function __construct(array $properties=array()) {
        $this->_properties = $properties;
    }
    
    //--------------------------------------------------------------------------
    //---                         public methods                             ---
    //-------------------------------------------------------------------------- 
    public function setServerAddress($hostport) {
        // TODO check base url and throw exception if invalid
        $this->_baseURL = 'http://'.$hostport.'/';
    }
    
    public function getSearchView($name='default', $reload=false) {
        // TODO add reload support 
        $searchViews = $this->getSearchViewList();
        if ($name!='default' && !in_array($name, $searchViews)) {
            throw new SearchEngineException("Searchview '$name' not available.");
        }
        
        $searchView = new SearchView($name);
        $searchView->initEngine($this);
        return $searchView;   
    }
    
    public function getSearchViewList() {
        if ($this->_searchViewList != null) {
            return $this->_searchViewList;
        }
        $url = $this->_baseURL."get?qrserverlist";
        
        // perform request
        if (isset($this->_properties['fastsearch.http.usecurl'])
            && $this->_properties['fastsearch.http.usecurl'] == true) {
            $resultString = $this->getResultString($url);
            $resultXML = @simplexml_load_string($resultString);
        } else {
            $resultXML = @simplexml_load_file($url);
        }
        
        $views = array();
        if($resultXML) {
            foreach($resultXML->view as $view) {
                $views[] = (string)$view['name'];
            }
        }
        $this->_searchViewList = $views;
        return $this->_searchViewList;
    }

    public function setSearchViewList(array $viewList) {
        $this->_searchViewList = $viewList;
    }
    
    public function search(IQuery $query) {
        
        // custom result view? 
        $resultView = 'search';
        try {
            $resultView = 
                $query->getParameterByType(BaseParameter::RESULT_VIEW)->getStringValue();
        } catch (NoSuchParameterException $e) {
            // no custom result view given 
        }
        
        // base search url
        $searchURL = $this->_baseURL."cgi-bin/xml-".$resultView."?";

        // custom search view?
        $searchView = 'default';
        try {
            $searchView = 
                $query->getParameterByType(BaseParameter::VIEW)->getStringValue();
            
            // append view parameter  
            // (note: must be first parameter cause other parameter dependencies)
            if ($searchView != 'default') {
                $searchURL .= "view=".rawurlencode($searchView);
            }
        } catch (NoSuchParameterException $e) {
            // no custom search view given 
        }
        
        // query string
        $queryStr = $query->getQueryString();
        
        // add filters to query string
        try {
            $filterStr = $query->getParameterByType(BaseParameter::FILTER);
            $queryStr = "and($queryStr, $filterStr)";
        } catch (NoSuchParameterException $e) {
            // no filter available
        }

        // append query parameter
        if (!empty($queryStr)) {
            if (substr($searchURL,-1) != '?') 
                $searchURL .= "&";    
            $searchURL .= "query=".rawurlencode($queryStr);
        }

        // Alveolar Click hack
        $searchURL = str_replace("%C7%82", "%01%C2", $searchURL);    
        
        // append other parameters
        $parameters = $query->getSearchParameters();
        
        foreach ($parameters as $parameter) {
            if ($parameter->getType() == BaseParameter::QUERY
                || $parameter->getType() == BaseParameter::FILTER
                || $parameter->getType() == BaseParameter::VIEW
                || $parameter->getType() == BaseParameter::RESULT_VIEW) 
                continue;
            if (substr($searchURL,-1) != '?') 
                $searchURL .= "&";    
            $searchURL .= $parameter->getName()
                ."=".rawurlencode($parameter->getStringValue());
        }
        
        // perform search
        if (isset($this->_properties['fastsearch.http.usecurl'])
            && $this->_properties['fastsearch.http.usecurl'] == true) {
            $resultString = $this->getResultString($searchURL);
            $resultXML = @simplexml_load_string($resultString);

            //@TODO remove <key>-replacement if scope search problem is solved
            if (!$resultXML) {
                // try to remove <key>-Tags in fields with name containing xml
                $resultXML = simplexml_load_string($this->removeXMLKeyTags($resultString));                        
            }
            
            
        } else {
            $resultString = @file_get_contents($searchURL);
            
            //@TODO remove str_replace if scope search problem is solved
            $resultXML = @simplexml_load_string($resultString);
            if (!$resultXML) {
                // try to remove <key>-Tags in fields with name containing xml
                $resultXML = @simplexml_load_string($this->removeXMLKeyTags($resultString));                        
            }
        }
            
        if (!$resultXML) {
            throw new SearchEngineException("Error while performing query (http " 
                ."request: $searchURL)");
        }
        
        $error = $resultXML->SEGMENT[0]->RESULTPAGE[0]->ERROR[0];        
        if ($error) {
            throw new SearchEngineException("Error while processing query "
                ."[".$error['CODE']."]: ".(string)$error);
        }
                
        // convert xml into IQueryResult resultset
        // get documents
        $documentSummaries = array();    
        $navigators = array();
        $queryTransformations = array();
        $segmentName = "";
        $totalHits   = 0;
        $maxRank     = 0;
        $timeUsed    = 0.0;
        
        $emptyResultSet = $resultXML->SEGMENT[0]->RESULTPAGE[0]->EMPTYRESULTSET[0]; 
        if ($emptyResultSet == null) {
            $segmentName = $resultXML->SEGMENT[0]['NAME'];
            $resultSet = $resultXML->SEGMENT[0]->RESULTPAGE[0]->RESULTSET[0];
            $totalHits = (int)$resultSet['TOTALHITS'];
            $maxRank   = (int)$resultSet['MAXRANK'];
            $timeUsed  = (float)$resultSet['TIME'];
            
            if (is_object($resultSet)) {
                foreach ($resultSet as $hit) {
                    $documentSummaryFields = array();
                    foreach ($hit as $field) {
    
                        // handle mixed content in field value
                        $fieldValue = (string)$field->asXML();
                        $fieldValue = str_replace("</FIELD>","",$fieldValue);
                        $fieldValue = ereg_replace("<FIELD[^>]*>","",$fieldValue);
    
                        // TODO use correct field types. at the moment only type 'STRING' is used. 
                        $documentSummaryField = 
                            new DocumentSummaryField((string)$field['NAME'],$fieldValue,
                                FieldType::STRING);
                        $documentSummaryFields[(string)$field['NAME']] = $documentSummaryField;                          
                    }
                    
                    // add standard fields
                    if (!array_key_exists('rank', $documentSummaryFields)) {
                        $documentSummaryFields['rank'] = 
                            new DocumentSummaryField('rank',(int)$hit['RANK'], FieldType::INTEGER);
                    }
                    if (!array_key_exists('siteid', $documentSummaryFields)) {
                        $documentSummaryFields['siteid'] = 
                            new DocumentSummaryField('siteid',(string)$hit['SITEID'], FieldType::STRING);
                    }
                    if (!array_key_exists('morehits', $documentSummaryFields)) {
                        $documentSummaryFields['morehits'] = 
                            new DocumentSummaryField('morehits',(int)$hit['MOREHITS'], FieldType::INTEGER);
                    }
                    if (!array_key_exists('fcocount', $documentSummaryFields)) {
                        $documentSummaryFields['fcocount'] = 
                            new DocumentSummaryField('fcocount',(int)$hit['FCOCOUNT'], FieldType::INTEGER);
                    }
                                            
                    // create and add final document summary
                    $documentSummary = new DocumentSummary($documentSummaryFields, 
                        (int)$hit['NO']);
                    $documentSummaries[] = $documentSummary;
                }
            }
                
            // add navigators to queryresult
            // TODO check if following also works if rpf:navigation is set to false             
            $navigation = $resultXML->SEGMENT[0]->RESULTPAGE[0]->NAVIGATION[0];
            $numEntries = (int)$navigation['ENTRIES'];
            foreach ($navigation as $navigator) {
                
                $navName        = (string)$navigator['NAME'];
                $navDisplayname = (string)$navigator['DISPLAYNAME'];
                $navType        = (string)$navigator['TYPE'];
                $navUnit        = (string)$navigator['UNIT'];
                $navModifier    = (string)$navigator['MODIFIER'];
                $navScore       = (float)$navigator['SCORE'];
                $navSampleCount = (int)$navigator['SAMPLECOUNT'];
                $navUsedHits    = (int)$navigator['USEDHITS'];
                $navHitCount    = (int)$navigator['HITCOUNT'];
                $navRatio       = (float)$navigator['RATIO'];
                $navMin         = (float)$navigator['MIN'];
                $navMax         = (float)$navigator['MAX'];
                $navMean        = (float)$navigator['MEAN'];
                $navEntropy     = (float)$navigator['ENTROPY'];
                // TODO frequency error
                $navFrequencyError = 0;
                
                $modifiers = array();
                foreach ($navigator->NAVIGATIONELEMENTS[0] as $navEntry) {
                    $modName     = (string)$navEntry['NAME'];
                    $modModifier = (string)$navEntry['MODIFIER'];
                    $modCount    = (int)$navEntry['COUNT'];
                    
                    if (ereg("^\[(.*);(.*)\]$", $modModifier)) {
                        $modIsInterval = true;
                    } else {
                        $modIsInterval = false;
                    }
                    //TODO calculate correct document ratio
                    $modifier = new Modifier($modName, $modCount, $modModifier, 
                        0.0, $navModifier, $modIsInterval);
                    $modifiers[] = $modifier;
                }
                
                // convert Types
                // TODO support Types different to String and DateTime
                $convNavType = 0;
                switch($navType) {
                    case 'DateTime':
                        $convNavType = NavigatorType::DATETIME;
                        break;
                    case 'String':
                    default:
                        $convNavType = NavigatorType::STRING;
                        break;
                    
                }
                $navigatorObj = new Navigator($navName, $navModifier, $navDisplayname,
                    $convNavType, $navUnit, $navScore, $navHitCount, $navUsedHits, 
                    $navRatio, $navSampleCount, $navMin, $navMax, $navMean, 
                    $navFrequencyError, $navEntropy, $modifiers);
                
                $navigators[] = $navigatorObj;                
            }
        }

        // query transformations
        $transforms = $resultXML->SEGMENT[0]->RESULTPAGE[0]->QUERYTRANSFORMS[0];
        foreach ($transforms as $transform) {
            $qtfName      = (string)$transform['NAME'];
            $qtfAction    = (string)$transform['ACTION'];
            $qtfQuery     = (string)$transform['QUERY'];
            $qtfCustom    = (string)$transform['CUSTOM'];
            $qtfMessage   = (string)$transform['MESSAGE'];
            $qtfMessageId = (int)$transform['MESSAGEID'];
            $queryTransformations[] = new QueryTransformation(
                $qtfName, $qtfAction, $qtfQuery, $qtfCustom, $qtfMessage, 
                $qtfMessageId);
        } 
        $queryResult = new QueryResult($query, new QueryTransformations($queryTransformations), 
            $documentSummaries, 
            $navigators, $segmentName, $totalHits, $maxRank, $timeUsed, 
            $searchURL);        
        return $queryResult;
    }
    
    //--------------------------------------------------------------------------
    //---                         private methods                            ---
    //-------------------------------------------------------------------------- 
    /**
     * @param string $url
     * @throws SearchEngineException
     *      
     */
    private function getResultString($url) {
        if (!function_exists('curl_init')) {
            throw new SearchEngineException('Error: libcurl not available: See '
                .'http://www.php.net/curl for details.');
        }
        
        $resource = curl_init($url);
        if (!$resource) {
            throw new SearchEngineException('Cannot init curl (URL: '.$url.')');
        }
        
        // default timeout (can be overriden)
        curl_setopt($resource, CURLOPT_CONNECTTIMEOUT, 2);
        
        // custom settings
        if (isset($this->_properties['fastsearch.http.curloptions'])) {
            $curlOpts = $this->_properties['fastsearch.http.curloptions'];
            if (is_array($curlOpts)) {
                foreach ($curlOpts as $opt => $value) {
                    curl_setopt($resource, $opt, $value); 
                }
            }
        }
        
        // retrieve content
        @curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($resource);
        if (!$content) {
            throw new SearchEngineException('Error processing HTTP Request: '
                .curl_error($resource).' (errno:'.curl_errno($resource).')');
        }
        
        curl_close($resource);
        return $content;
    }
    
    /**
     * Removes <key>-Tags in result xml string for all field with name containing 'xml'.
     * 
     * @param string $xmlString
     * @return string
     */
    private function removeXMLKeyTags($xmlString) {
        $lines = explode("\n", $xmlString);
        $finalXMLString = "";
        foreach ($lines as $line) {
            $regs = array();
            if (eregi('^<FIELD name="(.*xml.*)">', $line, $regs) 
                && eregi("<key>",$line)) {
                
                $fieldname = $regs['1'];
                $newFieldLine = "<!-- NOTE: field '$fieldname' manipulated by php search "
                    . "lib: removed <key>-Tags -->\n";
                $newFieldLine .= 
                    str_replace(array('<key>','</key>'),array('',''),$line)."\n";
                
                $finalXMLString .= $newFieldLine;
            } else {
                $finalXMLString .= $line."\n";
            }
        }
        return $finalXMLString;
    }
}
