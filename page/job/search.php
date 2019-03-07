<?php

/**
 * Container for job view
 */
class emol_page_job_search extends emol_page {
	/**
	 * EazyMatch 3.0 Api
	 *
	 * @var mixed
	 */
	var $emolApi;


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
		//first connect to the api
		$this->emolApi = eazymatch_connect();

		//set the page variables
		$this->page_title = get_option( 'emol_job_header' );

		if ( ! $this->emolApi ) {
			eazymatch_trow_error();
		}
	}

	/**
	 * get the search results
	 */
	function getContent() {

		//slaslehs html etc
		global $trailingData;

		//initialize the results
		$searchHtml = '';

		//get job controller
		$ws           = $this->emolApi->get( 'job' );
		$searchAction = explode( ',', $this->request_vars );

		//create an array with searchcriteria that is understandable
		$search   = array();
		$keyValue = '';


		foreach ( $searchAction as $searchValue ) {

			if ( substr( $searchValue, 0, 5 ) == 'page-' ) {
				//pagnation
				$pagnation       = (int) array_pop( explode( '-', $searchValue ) );
				$this->page_slug = str_replace( ',' . $searchValue, '', $this->page_slug );

			} elseif ( ! in_array( $searchValue, $this->searchCriteria ) && $keyValue > '' ) {

				//value
				$search[ $keyValue ][] = $searchValue;

			} elseif ( in_array( $searchValue, $this->searchCriteria ) ) {
				//criteria
				$search[ $searchValue ] = array();
				$keyValue               = $searchValue;

			}
		}

		// forced filters

		//status
		$filterOptions = emol_jobfilter_factory::createDefault()->getFilterArray();


		foreach ( $filterOptions as $filterKey => $filterValue ) {
			if ( ! isset( $search[ $filterKey ] ) ) {
				$search[ $filterKey ] = $filterValue;
				continue;
			}

			if ( is_array( $search[ $filterKey ] ) && is_array( $filterValue ) ) {
				foreach ( $filterValue as $filterItem ) {
					$search[ $filterKey ][] = $filterItem;
				}
			}
		}


		//location searches
		if ( isset( $search['location'] ) ) {
			$search['location']['range'] = (int) ( $search['location'][1] * 1000 );

			if ( is_numeric( $search['location'][0] ) || is_numeric( substr( $search['location'][0], 0, 4 ) ) ) {
				$search['location']['zipcode'] = ( $search['location'][0] );
			} else {
				$search['location']['city'] = ( $search['location'][0] );
			}
			emol_session::set( 'locationSearchZipcode', $search['location'][0] );
			emol_session::set( 'locationSearchRange', $search['location'][1] );
		} else {
			emol_session::remove( 'locationSearchZipcode' );
			emol_session::remove( 'locationSearchRange' );
		}

		//location searches
		if ( isset( $search['province'] ) && is_array( $search['province'] ) && count( $search['province'] ) > 0 ) {
			emol_session::set( 'locationSearchProvince', $search['province'][0] );
		} else {
			emol_session::remove( 'locationSearchProvince' );
		}

		//search criteria
		if ( isset( $search['free'] ) ) {
			emol_session::set( 'freeSearch', $search['free'][0] );
		} else {
			emol_session::remove( 'freeSearch' );
		}


		// get the searchresults
		$items_per_pagina = get_option( 'emol_job_amount_pp', 15 );
		$huidige_pagina   = 0;
		if ( isset( $pagnation ) && is_numeric( $pagnation ) && $pagnation > 0 ) {
			$huidige_pagina = $pagnation;
		}
		//echo $huidige_pagina;
		//determine offset
		$offset = $huidige_pagina * $items_per_pagina;

		$competences_display = get_option( 'emol_job_search_competence' );
		if ( ! empty( $competences_display ) ) {
			$search['advanced_competences'] = true;
		}

		$searchQuery = $ws->searchPublished( $search, $offset, $items_per_pagina );

		if ( $searchQuery['total'] == 0 ) {
			$searchHtml = '<div class="emol-no-results">' . stripslashes( get_option( 'emol_job_no_result' ) ) . '</div>';
		} else {

			$aantal_paginas = ceil( $searchQuery['total'] / $items_per_pagina );

			//get the actual result with pagination

			$nav = '<table class="emol-pagination-table"><tr>';
			$nav .= '<td class="emol-pagnation"><a href="' . get_bloginfo( 'wpurl' ) . '/' . $this->page_slug . $trailingData . '"> &lt;&lt;&lt; </a></td>';
			if ( $aantal_paginas > 1 && ( $huidige_pagina - 1 ) > 0 ) {
				$nav .= '<td class="emol-pagnation"><a href="' . get_bloginfo( 'wpurl' ) . '/' . $this->page_slug . ',page-' . ( $huidige_pagina - 1 ) . $trailingData . '"> &lt;&lt; </a></td>';
			}


			for ( $i = 0; $i < $aantal_paginas; $i ++ ) {

				if ( $huidige_pagina == $i ) {
					// huidige pagina is niet klikbaar
					$nav .= '<td class="emol-pagnation emol-pagnation-selected">' . ( $i + 1 ) . '</td>';
				} else {
					// een andere pagina
					if (
						( $i + 1 == $huidige_pagina ) ||
						( $i + 2 == $huidige_pagina ) ||
						( $i - 1 == $huidige_pagina ) ||
						( $i - 2 == $huidige_pagina )
					) {
						$nav .= '<td class="emol-pagnation"><a href="' . get_bloginfo( 'wpurl' ) . '/' . $this->page_slug . ',page-' . $i . $trailingData . '">' . ( $i + 1 ) . '</a></td>';
					}
				}

			}

			if ( $huidige_pagina + 1 != $aantal_paginas ) {
				$nav .= '<td class="emol-pagnation"><a href="' . get_bloginfo( 'wpurl' ) . '/' . $this->page_slug . ',page-' . ( $huidige_pagina + 1 ) . $trailingData . '"> &gt;&gt; </a></td>';
			}

			$nav .= '<td class="emol-pagnation last"><a href="' . get_bloginfo( 'wpurl' ) . '/' . $this->page_slug . ',page-' . ( $aantal_paginas - 1 ) . $trailingData . '"> &gt;&gt;&gt; </a></span></td>';
			$nav .= '</tr></table>';


			//title of result
			$searchHtml .= '<div id="emol-search-result-header">' . EMOL_JOBSEARCH_TOTAL . ' ' . $searchQuery['total'] . ', ' . EMOL_PAGE . '<b>' . ( $huidige_pagina + 1 ) . '</b>/' . $aantal_paginas . '</div>';
			$searchHtml .= '<div class="emol-page-navigation emol-page-navigation-top">' . $nav . '</div>';

			$emolRowColor = '';

			foreach ( $searchQuery['result'] as $job ) {

				if ( $emolRowColor == 'emol-odd' ) {
					$emolRowColor = 'emol-even';
				} else {
					$emolRowColor = 'emol-odd';
				}
				$searchHtml .= emol_parse_html_jobresult( $job, $emolRowColor );

			}
			$searchHtml .= '<div class="emol-page-navigation emol-page-navigation-bottom">' . $nav . '</div>';
		}

		return ( $searchHtml );
	}
}