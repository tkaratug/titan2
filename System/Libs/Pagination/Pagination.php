<?php
/*************************************************
 * Titan-2 Mini Framework
 * Pagination Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Pagination;

use System\Libs\Exception\ExceptionHandler;

class Pagination
{
	
	const NUM_PLACEHOLDER = '(:num)';

	// Total items to paginate
	protected $totalItems;

	// Total number of pages
	protected $numPages;

	// Items per page
	protected $itemsPerPage;

	// Current page
	protected $currentPage;

	// Url pattern
	protected $urlPattern;

	// Max pages to show in page navigator
	protected $maxPagesToShow = 10;

	// Text of preovious page link
	protected $previousText = 'Previous';

	// Text of next page link
	protected $nextText = 'Next';

	/**
	 * Initialize Pagination
	 *
	 * @param integer $totalItems
	 * @param integer $itemsPerPage
	 * @param integer $currentPage
	 * @param string $urlPattern
	 * @return void()
	 */
	public function init($totalItems, $itemsPerPage, $currentPage, $urlPattern = '')
	{
		$this->totalItems 	= $totalItems;
		$this->itemsPerPage = $itemsPerPage;
		$this->currentPage 	= $currentPage;
		$this->urlPattern 	= $urlPattern;
		$this->updateNumPages();
	}

	/**
	 * Update page numbers
	 *
	 * @return void
	 */
	protected function updateNumPages()
	{
		$this->numPages = ($this->itemsPerPage == 0 ? 0 : (int) ceil($this->totalItems/$this->itemsPerPage));
	}

	/** 
	 * Set Max. pages to show
	 *
	 * @param integer $maxPageToShow
	 */
	public function setMaxPagesToShow($maxPagesToShow)
	{
		if ($maxPagesToShow < 3)
			throw new ExceptionHandler('Library Error','maxPagesToShow cannot be less than 3.');

		$this->maxPagesToShow = $maxPagesToShow;
	}

	/** 
	 * Get max. pages to show
	 *
	 * @return integer
	 */
	public function getMaxPagesToShow()
	{
		return $this->maxPagesToShow;
	}

	/**
	 * Set current page
	 *
	 * @param integer $currentPage
	 * @return void
	 */
	public function setCurrentPage($currentPage)
	{
		$this->currentPage = $currentPage;
	}

	/**
	 * Get current page number
	 *
	 * @return integer
	 */
	public function getCurrentPage()
	{
		return $this->currentPage;
	}

	/**
	 * Set items per page
	 *
	 * @param integer $itemsPerPage
	 * @return void
	 */
	public function setItemsPerPage($itemsPerPage)
	{
		$this->itemsPerPage = $itemsPerPage;
		$this->updateNumPages();
	}

	/**
	 * Get items per page
	 *
	 * @return integer
	 */
	public function getItemsPerPage()
	{
		return $this->itemsPerPage;
	}

	/**
	 * Set total items to paginate
	 *
	 * @param integer $totalItems
	 * @return void
	 */
	public function setTotalItems($totalItems)
	{
		$this->totalItems = $totalItems;
		$this->updateNumPages();
	}

	/**
	 * Get total items to paginate
	 *
	 * @return integer
	 */
	public function getTotalItems()
	{
		return $this->totalItems;
	}

	/**
	 * Get number of pages
	 *
	 * @return integer
	 */
	public function getNumPages()
	{
		return $this->numPages;
	}

	/**
	 * Set url pattern
	 *
	 * @param string $urlPattern
	 * @return void
	 */
	public function setUrlPattern($urlPattern)
	{
		$this->urlPattern = $urlPattern;
	}

	/**
	 * Get url pattern
	 *
	 * @return string
	 */
	public function getUrlPattern()
	{
		return $this->urlPattern;
	}

	/**
	 * Get page url
	 *
	 * @param integer $pageNum
	 * @return string
	 */
	public function getPageUrl($pageNum)
	{
		return str_replace(self::NUM_PLACEHOLDER, $pageNum, $this->urlPattern);
	}

	/**
	 * Get next page
	 *
	 * @return integer|null
	 */
	public function getNextPage()
	{
		if ($this->currentPage < $this->numPages)
			return $this->currentPage + 1;

		return null;
	}

	/**
	 * Get previous page
	 *
	 * @return integer|null
	 */
	public function getPrevPage()
	{
		if ($this->currentPage > 1)
			return $this->currentPage - 1;

		return null;
	}

	/**
	 * Get url of the next page
	 *
	 * @return string|null
	 */
	public function getNextUrl()
	{
		if (!$this->getNextPage())
			return null;

		return $this->getPageUrl($this->getNextPage());
	}

	/**
	 * Get url of the previous page
	 *
	 * @return string|null
	 */
	public function getPrevUrl()
	{
		if (!$this->getPrevPage())
			return null;

		return $this->getPageUrl($this->getPrevPage());
	}

	/**
	 * Get pages
	 *
	 * @return array
	 */
	public function getPages()
	{
		$pages = array();

		if ($this->numPages <= 1)
			return array();

		if ($this->numPages <= $this->maxPagesToShow) {
			for ($i = 1; $i <= $this->numPages; $i++) {
				$pages[] = $this->createPage($i, $i == $this->currentPage);
			}
		} else {
			// Determine the sliding range, centered around the current page.
			$numAdjacents = (int) floor(($this->maxPagesToShow - 3) / 2);

			if ($this->currentPage + $numAdjacents > $this->numPages)
				$slidingStart = $this->numPages - $this->maxPagesToShow + 2;
			else
				$slidingStart = $this->currentPage - $numAdjacents;

			if ($slidingStart < 2) 
				$slidingStart = 2;

			$slidingEnd = $slidingStart + $this->maxPagesToShow - 3;

			if ($slidingEnd >= $this->numPages) 
				$slidingEnd = $this->numPages - 1;

			// Build the list of pages.
			$pages[] = $this->createPage(1, $this->currentPage == 1);

			if ($slidingStart > 2)
				$pages[] = $this->createPageEllipsis();

			for ($i = $slidingStart; $i <= $slidingEnd; $i++) {
				$pages[] = $this->createPage($i, $i == $this->currentPage);
			}

			if ($slidingEnd < $this->numPages - 1)
				$pages[] = $this->createPageEllipsis();

			$pages[] = $this->createPage($this->numPages, $this->currentPage == $this->numPages);
		}

		return $pages;
	}

	/**
	 * Create page link
	 *
	 * @param integer $pageNum
	 * @param boolean $isCurrent
	 * @return array
	 */
	protected function createPage($pageNum, $isCurrent = false)
	{
		return array(
			'num' => $pageNum,
			'url' => $this->getPageUrl($pageNum),
			'isCurrent' => $isCurrent,
		);
	}

	/**
	 * Create page ellipsis
	 *
	 * @return array
	 */
	protected function createPageEllipsis()
	{
		return array(
			'num' => '...',
			'url' => null,
			'isCurrent' => false,
		);
	}

	/**
	 * Print page links
	 *
	 * @return string
	 */
	public function printLinks()
	{
		if ($this->numPages <= 1)
			return '';

		$html = '<ul class="pagination">';

		if ($this->getPrevUrl())
			$html .= '<li><a href="' . $this->getPrevUrl() . '">&laquo; '. $this->previousText .'</a></li>';

		foreach ($this->getPages() as $page) {
			if ($page['url'])
				$html .= '<li' . ($page['isCurrent'] ? ' class="active"' : '') . '><a href="' . $page['url'] . '">' . $page['num'] . '</a></li>';
			else
				$html .= '<li class="disabled"><span>' . $page['num'] . '</span></li>';
		}

		if ($this->getNextUrl())
			$html .= '<li><a href="' . $this->getNextUrl() . '">'. $this->nextText .' &raquo;</a></li>';

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Get the first item of current page
	 *
	 * @return integer|null
	 */
	public function getCurrentPageFirstItem()
	{
		$first = ($this->currentPage - 1) * $this->itemsPerPage + 1;

		if ($first > $this->totalItems)
			return null;

		return $first;
	}

	/**
	 * Get the last item of current page
	 *
	 * @return integer|null
	 */
	public function getCurrentPageLastItem()
	{
		$first = $this->getCurrentPageFirstItem();

		if ($first === null)
			return null;

		$last = $first + $this->itemsPerPage - 1;

		if ($last > $this->totalItems)
			return $this->totalItems;

		return $last;
	}

	/**
	 * Set the previous link text
	 *
	 * @param string $text
	 * @return obj
	 */
	public function setPreviousText($text)
	{
		$this->previousText = $text;
		return $this;
	}

	/**
	 * Set the next link text
	 *
	 * @param string $text
	 * @return obj
	 */
	public function setNextText($text)
	{
		$this->nextText = $text;
		return $this;
	}

}