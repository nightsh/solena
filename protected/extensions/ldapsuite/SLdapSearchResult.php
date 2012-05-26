<?php

class SLdapSearchResult extends CComponent implements Iterator
{
	/**
	 * Net_LDAP2_Search instance which this object wraps to provide it's services
	 */
	protected $_search = null;
	/**
	 * Cache of our internal validity.
	 * We need to be able to continue to operate - even if the search is invalid to contain complexity from other areas
	 */
	protected $_validResults = false;

	/**
	 * Sorting instance used to retrieve sorting settings
	 */
	protected $_sorter = null;
	
	/**
	 * Pagination instance used to retrieve pagination settings
	 */
	protected $_paginator = null;
	
	/**
	 * Class which will be used to Model the results.
	 * Results will be returned as Net_LDAP2_Entry objects if not set.
	 */
	protected $_modelClass = null;

	/**
	 * Cache of the sorted and paginated results - extracted from $_search
	 */
	protected $_resultsCache = array();

	/**
	 * Constructor
	 *
	 * @param Net_LDAP2_Search $search    Net_LDAP2_Search object
	 * @param CSort            $sorter    
	 * @param CPaginator       $paginator Paginator instance that will be used to apply pagination
	 */
	public function __construct($search, $paginator = null, $sorter = null)
	{
		// Copy across the search instance (even if it is invalid...)
		if( $search instanceof Net_LDAP2_Search && $search->count() > 0 ) {
			$this->_validResults = true;
		}
		$this->_search = $search;
		
		// Copy over the sorter and paginator if needed...
		if( $sorter instanceof CSort ) {
			$this->_sorter = $sorter;
		}
		if( $paginator instanceof CPagination ) {
			$this->_paginator = $paginator;
		}
	}
	
	/**
	 * Set the Model class which this Search Result instance will be using.
	 */
	public function setModelClass($class)
	{
		$this->_modelClass = $class;
	}
	
	/**
	 * Load the results, then sort, page them and convert them into the needed model entries if needed
	 */
	private function loadResults()
	{
		// Do we have a valid set of search results?
		if( !$this->_validResults ) {
			return;
		}

		// Load the search results
		$results = $this->_search->entries();

		// Apply sorting....
		if( $this->_sorter instanceof CSort ) {
			// Determine what we need to sort
			$metadata = $this->_sorter->getDirections();
			$data = array();

			// Prepare to do the sort....
			foreach( $results as $key => $entry ) {
				foreach( $metadata as $attr => $desc ) {
					$data[$attr][$key] = $entry->getValue($attr);
				}
			}

			// Build up the array_multisort call
			$multisort = array();
			foreach( $metadata as $attr => $desc ) {
				$direction = $desc ? SORT_DESC : SORT_ASC;
				$multisort[] = &$data[$attr];
				$multisort[] = &$direction;
			}
			$multisort[] = &$results;

			call_user_func_array( "array_multisort", $multisort );
		}

		// Apply pagination
		if( $this->_paginator instanceof CPagination ) {
			$offset = $this->_paginator->getOffset();
			$limit = $this->_paginator->getLimit();
			$results = array_slice($results, $offset, $limit);
		}

		// Transform into Model classes....
		if( !is_null($this->_modelClass) ) {
			$model = SLdapModel::model($this->_modelClass);
			foreach($results as $key => $entry) {
				$results[$key] = $model->createInstance($entry);
			}
		}

		$this->_resultsCache = $results;
		reset($this->_resultsCache);
	}

	/**
	 * Returns an array of entry objects
	 *
	 * @return array Array of entry objects.
	 */
	public function entries()
	{
		if( empty($this->_resultsCache) ) {
			$this->loadResults();
		}
		return $this->_resultsCache;
	}

	/**
	 * Returns the number of entries in the searchresult
	 *
	 * @return int Number of entries in search.
	 */
	public function count()
	{
		return $this->_validResults ? $this->_search->count() : 0;
	}

	/**
	 * Get the errorcode the object got in its search.
	 *
	 * @return int The ldap error number.
	 */
	public function getErrorCode()
	{
		return $this->_validResults ? $this->_search->getErrorCode() : 0;
	}

	/**
	 * Has the search exceeded the size limits imposed on it (by either the server or client)
	 */
	public function sizeLimitExceeded()
	{
		return $this->_validResults ? $this->_search->sizeLimitExceeded() : false;
	}

	/**
	 * What error did the search encounter?
	 */
	public function getErrorMessage()
	{
		return PEAR::isError($this->_search) ? $this->_search->message() : "";
	}

	/**
	 * SPL Iterator interface: Return the current element.
	 */
	public function current()
	{
		if( empty($this->_resultsCache) ) {
			$this->loadResults();
		}
		$entry = current($this->_resultsCache);
		return ($entry instanceof Net_LDAP2_Entry || $entry instanceof SLdapModel) ? $entry : false;
    }

	/**
	* SPL Iterator interface: Return the identifying key (DN) of the current entry.
	*/
	public function key()
	{
		$entry = $this->current();
		if( $entry instanceof Net_LDAP2_Entry ) {
			return $entry->dn();
		} else if( $entry instanceof SLdapModel ) {
			return $entry->getDn();
		}
		return false;
	}

	/**
	 * SPL Iterator interface: Move forward to next entry.
	 */
	public function next()
	{
		next($this->_resultsCache);
	}

	/**
	 * SPL Iterator interface:  Check if there is a current element after calls to {@link rewind()} or {@link next()}.
	 */
	public function valid()
	{
		$entry = $this->current();
		return ($entry instanceof Net_LDAP2_Entry || $entry instanceof SLdapModel);
	}

	/**
	 * SPL Iterator interface: Rewind the Iterator to the first element.
	 */
	public function rewind()
	{
		reset($this->_resultsCache);
	}
}

?>