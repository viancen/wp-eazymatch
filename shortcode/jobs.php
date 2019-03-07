<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Get published joblist
 *
 */
class emol_shortcode_jobs {

	public $competences;

	function getContent() {

		global $emol_side;
		global $trailingData;
		global $emol_api;

		$emol_side = 'applicant';
		$emol_api  = eazymatch_connect();

		if ( $emol_api ) {
			$jobs = array();

			$limit = 5;
			if ( is_numeric( get_option( 'emol_job_amount_pp' ) ) && get_option( 'emol_job_amount_pp' ) > 0 ) {
				$limit = get_option( 'emol_job_amount_pp' );
			}


			$filterOptions = emol_jobfilter_factory::createDefault()->getFilterArray();

			if ( is_array( $this->competences ) && count( $this->competences ) > 0 ) {
				$filterOptions['competence'] = $this->competences;
			}

			$wsJob = $emol_api->get( 'job' );

			$competences_display = get_option( 'emol_job_search_competence' );
			if ( ! empty( $competences_display ) ) {
				$filterOptions['advanced_competences'] = true;
			}

			$jobs = $wsJob->getPublished( $limit, $filterOptions );

			//navigation
			$total = count( $jobs );

			$emolRowColor = '';
			$i            = 0;
			$text         = '<div class="eazymatch-job-list">'; //<div id="eazymatch-job-list-header">'.get_option('emol_job_header').'</div>';

			if ( $total > 0 ) {
				foreach ( $jobs as $job ) {

					$i ++;
					if ( $i > $limit ) {
						break;
					}

					if ( $emolRowColor == 'emol-odd' ) {
						$emolRowColor = 'emol-even';
					} else {
						$emolRowColor = 'emol-odd';
					}
					$text .= emol_parse_html_jobresult( $job, $emolRowColor );
				}

				//$text .= '<div class="emol-pagnation-readmore"><a href="'.get_bloginfo( 'wpurl').'/'.get_option( 'emol_job_search_url' ).'/all'.$trailingData.'">'.EMOL_JOBSEARCH_MORE.'</a></div>';


			} else {
				$text .= '<div class="emol-no-results">' . get_option( 'emol_job_no_result' ) . '</div>';
			}
			$text .= '</div>';

			return ( $text );

		} else {
			unset( $_SESSION['emol'] );
			eazymatch_trow_error( 'Geen connectie met EazyMatch -> stel eerst een verbinding in via het CMS' );
		}

	}
}

