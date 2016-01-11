<?php

//搜索参数类
class QueryParameters {
	private $view;
	private $query;
	private $hitsPerPage;
	private $offset;
	private $similarto;
	private $similartype;
	private $sortBy;
	private $queryString;
	private $filterTerms;
    private $breadcrumbs;
    private $additionFQL;
	private $drillType;
	private $drillName;
	private $additionRelateKeys;
	private $topicFilterId;
	private $isShowNav = 1;
	private $newsFilterId;
	private $slot1 = '';
	private $slot2 = '';
	private $sources;
	private $advancedQueryStr;
	
	public function getAdditionFQL() {
		return $this->additionFQL;
	}
	public function setAdditionFQL($additionFQL) {
		$this->additionFQL = $additionFQL;
	}
	public function getBreadcrumbs() {
		return $this->breadcrumbs;
	}
	public function setBreadcrumbs($breadcrumbs) {
		$this->breadcrumbs = $breadcrumbs;
	}
	public function getFilterTerms() {
		return $this->filterTerms;
	}
	public function setFilterTerms($filterTerms) {
		$this->filterTerms = $filterTerms;
	}
	public function getQueryString() {
		return $this->queryString;
	}
	public function setQueryString($queryString) {
		$this->queryString = $queryString;
	}
	public function getSortBy() {
		return $this->sortBy;
	}
	public function setSortBy($sortBy) {
		$this->sortBy = $sortBy;
	}
	public function getView() {
		return $this->view;
	}
	public function setView($view) {
		$this->view = $view;
	}
	public function getQuery() {
		return $this->query;
	}
	public function setQuery($query) {
		$this->query = $query;
	}
	public function getHitsPerPage() {
		return $this->hitsPerPage;
	}
	public function setHitsPerPage($hitsPerPage) {
		$this->hitsPerPage = $hitsPerPage;
	}
	public function getOffset() {
		return $this->offset;
	}
	public function setOffset($offset) {
		$this->offset = $offset;
	}
	public function getSimilarto() {
		return $this->similarto;
	}
	public function setSimilarto($similarto) {
		$this->similarto = $similarto;
	}
	public function getSimilartype() {
		return $this->similartype;
	}
	public function setSimilartype($similartype) {
		$this->similartype = $similartype;
	}
	public function getDrillType() {
		return $this->drillType;
	}
	public function setDrillType($drillType) {
		$this->drillType = $drillType;
	}
	public function getDrillName() {
		return $this->drillName;
	}
	public function setDrillName($drillName) {
		$this->drillName = $drillName;
	}
	public function getAdditionRelateKeys() {
		return $this->additionRelateKeys;
	}
	public function setAdditionRelateKeys($additionRelateKeys) {
		$this->additionRelateKeys = $additionRelateKeys;
	}
	public function getTopicFilterId() {
		return $this->topicFilterId;
	}
	public function setTopicFilterId($topicFilterId) {
		$this->topicFilterId = $topicFilterId;
	}
	public function getParamsArr() {
		return get_object_vars($this);
	}
	public function getIsShowNav() {
		return $this->isShowNav;
	}
	public function setIsShowNav($isShowNav) {
		$this->isShowNav = $isShowNav;
	}
	public function getNewsFilterId() {
		return $this->newsFilterId;
	}
	public function setNewsFilterId($newsFilterId) {
		$this->newsFilterId = $newsFilterId;
	}
	
	public function getSlot1() {
		return $this->slot1;
	}
	public function setSlot1($slot1) {
		$this->slot1 = $slot1;
	}
	public function getSlot2() {
		return $this->slot2;
	}
	public function setSlot2($slot2) {
		$this->slot2 = $slot2;
	}
	public function getSources() {
		return $this->sources;
	}
	public function setSource($sources) {
		$this->sources = $sources;
	}
	public function getAdvancedQueryStr() {
		return $this->advancedQueryStr;
	}
	public function setAdvancedQueryStr($advancedQueryStr) {
		$this->advancedQueryStr = $advancedQueryStr;
	}
}