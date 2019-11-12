<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Get published joblist, this creates a complete page with navigation
 *
 */
class emol_shortcode_jobpage {

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

	function getContent() {

		$reqvars =( get_query_var( 'emol_query' ) );


		return emol_get_job_search_results( $reqvars, get_option('emol_job_search_page'), $this->searchCriteria );

	}
}

