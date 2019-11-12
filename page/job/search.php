<?php

/**
 * Container for job view
 */
class emol_page_job_search extends emol_page {


	/**
	 * allowed tags to be filtered on
	 *
	 * @var mixed
	 */
	var $searchCriteria = array(
		'competence',
		'free',
		'location',
		'province'
	);

	protected function preparePost() {
		//set the page variables
		$this->page_title = get_option( 'emol_job_header' );
	}

	/**
	 * get the search results
	 */
	function getContent() {
		return emol_get_job_search_results( $this->request_vars, $this->page_slug, $this->searchCriteria );
	}
}
