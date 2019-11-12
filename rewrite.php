<?php
/**
 * Adds variables that are given by eazymatch to the Wordpress $_GET system
 *
 * @param mixed $public_query_vars
 */
function eazymatch_add_var( $public_query_vars ) {

	$public_query_vars[] = 'emol_job_id';
	$public_query_vars[] = 'emol_apply_id';
	$public_query_vars[] = 'emolpage';
	$public_query_vars[] = 'emolaction';
	$public_query_vars[] = 'emolparameters'; //for seo texts or /apply or whatever.
	$public_query_vars[] = 'emolrequestid'; //for seo texts or /apply or whatever.

	return $public_query_vars;
}

add_filter( 'query_vars', 'eazymatch_add_var' );


/**
 * Creates rewrite rules so the site will react to a url set in the CMS
 *
 */
function eazymatch_do_rewrite() {

	#new skool
	add_rewrite_rule( get_option( 'emol_job_page' ) . '/([^/]+)/?$', 'index.php?pagename=' . get_option( 'emol_job_page' ) . '&emol_job_id=$matches[1]', 'top' );
	add_rewrite_rule( get_option( 'emol_apply_page' ) . '/([^/]+)/?$', 'index.php?pagename=' . get_option( 'emol_apply_page' ) . '&emol_job_id=$matches[1]', 'top' );

	#old skool
	add_rewrite_rule( get_option( 'emol_job_url' ) . '/([^/]+)/([^/]+)/?$', 'index.php?emolpage=' . get_option( 'emol_job_url' ) . '&emolaction=$matches[1]&emolparameters=$matches[2]', 'top' );
	add_rewrite_rule( get_option( 'emol_apply_url' ) . '/([^/]+)/([^/]+)/?$', 'index.php?emolpage=' . get_option( 'emol_apply_url' ) . '&emolaction=$matches[1]&emolparameters=$matches[2]', 'top' );
	add_rewrite_rule( get_option( 'emol_account_url' ) . '/([^/]+)/?$', 'index.php?emolpage=' . get_option( 'emol_account_url' ) . '&emolaction=$matches[1]', 'top' );
	add_rewrite_rule( get_option( 'emol_job_search_url' ) . '/([^/]+)/?$', 'index.php?emolpage=' . get_option( 'emol_job_search_url' ) . '&emolaction=$matches[1]', 'top' );
	add_rewrite_rule( get_option( 'emol_apply_url_free' ) . '/?$', 'index.php?emolpage=' . get_option( 'emol_apply_url_free' ) . '&emolaction=$matches[1]', 'top' );

	/**rss feed**/
	add_rewrite_rule( 'em-jobfeed/([^/]+)/?$', 'index.php?emolpage=rss&emolaction=$matches[1]', 'top' );
	add_rewrite_rule( 'em-submit-subscription', 'index.php?emolpage=submit_subscription', 'top' );

	/**cv urls**/
	add_rewrite_rule( get_option( 'emol_cv_url' ) . '/([^/]+)/([^/]+)/?$', 'index.php?emolpage=' . get_option( 'emol_cv_url' ) . '&emolaction=$matches[1]&emolparameters=$matches[2]', 'top' );
	add_rewrite_rule( get_option( 'emol_react_url_cv' ) . '/([^/]+)/([^/]+)/?$', 'index.php?emolpage=' . get_option( 'emol_react_url_cv' ) . '&emolaction=$matches[1]&emolparameters=$matches[2]', 'top' );
	add_rewrite_rule( get_option( 'emol_company_account_url' ) . '/([^/]+)/?$', 'index.php?emolpage=' . get_option( 'emol_company_account_url' ) . '&emolaction=$matches[1]', 'top' );
	add_rewrite_rule( get_option( 'emol_cv_search_url' ) . '/([^/]+)/?$', 'index.php?emolpage=' . get_option( 'emol_cv_search_url' ) . '&emolaction=$matches[1]', 'top' );
}

add_action( 'init', 'eazymatch_do_rewrite' );

/**
 * function that is hooked to the Wordpress queryparsing system
 * when EMOL parameters are passed, this will create a fake eazymatch page
 * with the right content (see emol-page.php);
 */
function emol_parse_query( $wp_query ) {

	$allVars = $wp_query->query_vars;

	//if we have a page (but not the job_page option) this one handles just the shortcode for a jobpage
	if ( isset( $allVars['emolpage'] ) && $allVars['emolpage'] != '' && $allVars['emolpage'] != get_option( 'emol_job_page' ) ) {


		$EmolPage     = get_query_var( 'emolpage', false ); //the emol page
		$EmolFunction = get_query_var( 'emolaction', false ); //id or handle data
		$EmolParams   = get_query_var( 'emolparameters', false ); //mostly sef string

		$wp_query->is_single  = false;
		$wp_query->is_page    = true;
		$wp_query->is_archive = false;
		$wp_query->is_search  = false;
		$wp_query->is_home    = false;

		//we need to combine all data to have our fake page do its work
		$emolSlug = $EmolFunction;
		if ( isset( $EmolParams ) && $EmolParams != '' ) {
			$emolSlug .= '/' . $EmolParams;
		}

		//what side are we on?
		global $emol_side;


		/**
		 * Create an instance the emol Fake Page
		 */
		switch ( $EmolPage ) {
			case 'rss':
				$dummyPage = 'emol_page_job_rss';
				break;
			case 'submit_subscription':

				$data = emol_post_application();
				wp_redirect($data);
				break;

			case get_option( 'emol_job_url' ):
				$emol_side = 'applicant';
				$dummyPage = 'emol_page_job_view';
				break;

			case get_option( 'emol_apply_url' ):
				$emol_side = 'applicant';
				$dummyPage = 'emol_page_applicant_apply';
				break;

			case get_option( 'emol_apply_url_free' ):
				ob_clean();
				header( 'location: ' . get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/0/open' );
				exit();
				break;

			case get_option( 'emol_account_url' ):
				$emol_side = 'applicant';
				$dummyPage = 'emol_page_applicant_account_edit';
				break;

			case get_option( 'emol_job_search_url' ):
				$emol_side = 'applicant';
				$dummyPage = 'emol_page_job_search';
				break;

			case get_option( 'emol_cv_url' ):
				$emol_side = 'company';
				$dummyPage = 'emol_page_cv_view';
				break;

			case get_option( 'emol_react_url_cv' ):
				$emol_side = 'company';
				$dummyPage = 'emol_page_company_react';
				break;

			case get_option( 'emol_company_account_url' ):
				$emol_side = 'company';
				$dummyPage = 'emol_page_company_account_edit';
				break;

			case get_option( 'emol_cv_search_url' ):
				$emol_side = 'company';
				$dummyPage = 'emol_page_cv_search';
				break;
		}

		if ( isset( $dummyPage ) ) {

			$emolDummyPage = new $dummyPage( $EmolPage, $emolSlug );
		}
	}
}

add_action( 'parse_query', 'emol_parse_query' );
