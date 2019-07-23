<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Get published joblist, this creates a complete page with navigation
 *
 */
class emol_shortcode_jobpage {


	function getContent() {

		global $emol_side;
		global $trailingData;
		global $emol_api;

		$emol_side = 'applicant';
		$emol_api  = eazymatch_connect();

		if ( $emol_api ) {
			$jobs = array();

			$offset           = 0;
			$items_per_pagina = 5;

			if ( is_numeric( get_option( 'emol_job_amount_pp' ) ) && get_option( 'emol_job_amount_pp' ) > 0 ) {
				$items_per_pagina = get_option( 'emol_job_amount_pp' );
			}

			$huidige_pagina = 0;
			if ( isset( $_GET['jpage'] ) && is_numeric( $_GET['jpage'] ) ) {
				$huidige_pagina = (int) $_GET['jpage'];
				$offset         = $huidige_pagina * $items_per_pagina;
			}

			$filterOptions = emol_jobfilter_factory::createDefault()->getFilterArray();

			$competences_display = get_option( 'emol_job_search_competence' );
			if ( ! empty( $competences_display ) ) {
				$filterOptions['advanced_competences'] = true;
			}
			//$jobs  = $emol_api->get('job')->getPublished($limit,$filterOptions);
			$jobs = $emol_api->get( 'job' )->searchPublished( $filterOptions, $offset, $items_per_pagina );
			// emol_dump($jobs);
			$total = $jobs['total'];
			$jobs  = $jobs['result'];

			if ( $total == 0 ) {
				$text = '<div class="emol-no-results">' . get_option( 'emol_job_no_result' ) . '</div>';
			} else {

				$aantal_paginas = ceil( $total / $items_per_pagina );

				$nav = '<table class="emol-pagination-table"><tr>';
				$nav .= '<td class="emol-pagnation"><a href="?jpage=0"> &lt;&lt;&lt; </a></td>';
				if ( $aantal_paginas > 1 && ( $huidige_pagina - 1 ) > 0 ) {
					$nav .= '<td class="emol-pagnation"><a href="?jpage=' . ( $huidige_pagina - 1 ) . '"> &lt;&lt; </a></td>';
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
							$nav .= '<td class="emol-pagnation"><a href="?jpage=' . $i . '">' . ( $i + 1 ) . '</a></td>';
						}
					}
				}

				if ( $huidige_pagina + 1 != $aantal_paginas ) {
					$nav .= '<td class="emol-pagnation"><a href="?jpage=' . ( $huidige_pagina + 1 ) . '"> &gt;&gt; </a></td>';
				}

				$nav .= '<td class="emol-pagnation last"><a href="?jpage=' . ( $aantal_paginas - 1 ) . '"> &gt;&gt;&gt; </a></span></td>';
				$nav .= '</tr></table>';

				//title of result
				$text = '<div id="emol-search-result-header">' . EMOL_JOBSEARCH_TOTAL . ' ' . $total . ', ' . EMOL_PAGE . '<b>' . ( $huidige_pagina + 1 ) . '</b>/' . $aantal_paginas . '</div>';
				$text .= '<div class="emol-page-navigation emol-page-navigation-top">' . $nav . '</div>';

				$emolRowColor = '';
				$i            = 0;
				$text         .= '<div class="eazymatch-job-list">';
				foreach ( $jobs as $job ) {

					$i ++;
					if ( $i > $items_per_pagina ) {
						break;
					}

					if ( $emolRowColor == 'emol-odd' ) {
						$emolRowColor = 'emol-even';
					} else {
						$emolRowColor = 'emol-odd';
					}
					$text .= emol_parse_html_jobresult( $job, $emolRowColor );
				}

				$text .= '<div class="emol-pagnation-readmore">' . $nav . '</div>';
				$text .= '</div>';
			}

			return ( $text );

		} else {
			unset( $_SESSION['emol'] );
			eazymatch_trow_error( 'Geen connectie met EazyMatch -> stel eerst een verbinding in via het CMS' );
		}
	}
}

