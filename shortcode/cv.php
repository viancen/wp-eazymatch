<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Get published cvlist
 *
 */
class emol_shortcode_cv {

	function getContent() {
		global $trailingData;
		global $emol_side;
		$emol_side = 'company';

		$api = eazymatch_connect();

		if ( $api ) {
			$cvs = array();

			$limit = 5;
			if ( is_numeric( get_option( 'emol_cv_amount_pp' ) ) && get_option( 'emol_cv_amount_pp' ) > 0 ) {
				$limit = get_option( 'emol_cv_amount_pp' );
			}

			try {
				$wsCV = $api->get( 'applicant' );
				$cvs  = $wsCV->getPublished( $limit );
			} catch ( SoapFault $e ) {
				eazymatch_trow_error( 'Fout in request EazyMatch -> cv' );
				echo "<pre>";
				print_r( $e );
			}

			$text = '';

			//navigation
			$total = count( $cvs );

			//get a option
			$picVisible  = get_option( 'emol_cv_search_picture' );
			$descVisible = get_option( 'emol_cv_search_desc' );

			$emolRowColor = '';

			//$wsPers = $api->get('person');
			$i = 0;
			if ( $total > 0 ) {
				$text = '<div class="emol-cv-block">';
				foreach ( $cvs as $cv ) {
					$i ++;
					if ( $i > $limit ) {
						break;
					}

					if ( $emolRowColor == 'emol-odd' ) {
						$emolRowColor = 'emol-even';
					} else {
						$emolRowColor = 'emol-odd';
					}
					$text .= '<div class="emol-cv-result-item ' . $emolRowColor . '">';

					$cv_url = get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_cv_url' ) . '/' . $cv['id'] . '/' . eazymatch_friendly_seo_string( $cv['title'] ) . $trailingData;

					$img = '';
					if ( isset( $cv['Person']['Picture'] ) && $cv['Person']['Picture']['content'] != '' && $picVisible == 1 ) {
						$img = '<div class="emol-cv-result-picture">
                        <a href="' . $cv_url . '"><img src="data:image/png;base64,' . $cv['Person']['Picture']['content'] . '" /></a>
                    </div>';
					} elseif ( $picVisible == 1 ) {
						$img = '<div class="emol-cv-result-picture">
                        <a href="' . $cv_url . '"><img src="' . get_bloginfo( 'wpurl' ) . '/wp-content/plugins/eazymatch/assets/img/blank-icon.png" alt="" /></a></div>';
					}


					$react_url = '/' . get_option( 'emol_react_url_cv' ) . '/' . $cv['id'] . '/' . eazymatch_friendly_seo_string( $cv['title'] ) . $trailingData;

					/**image*/
					$text .= $img;

					/**title*/
					$text .= '<div class="emol-cv-title"><a href="' . $cv_url . '">' . $cv['title'] . '</a></div>';

					/**prefered address*/
					if ( isset( $cv['Person']['Preferedaddress']['city'] ) ) {
						$text .= '<div class="emol-cv-city"><a href="' . $cv_url . '">' . strtoupper( $cv['Person']['Preferedaddress']['city'] ) . '</a></div>';
					}

					/**is the body of CV visible*/
					if ( $descVisible == 1 ) {
						$text .= '<div class="emol-cv-body">' . $cv['description'] . '</div>';
					}

					/**toolbar*/
					$text .= '<div class="emol-cv-toolbar"><a href="' . $react_url . '">' . EMOL_CVSEARCH_APPLY . '</a>
                    <a href="' . $cv_url . '">' . EMOL_SEARCH_READMORE . '</a></div>';

					/**seperator of results*/
					$text .= '<div class="emol-result-seperator"></div>';
					$text .= '</div>';
				}
				$text .= '<div class="emol-pagnation-readmore"><a href="/' . get_option( 'emol_cv_search_url' ) . '/all/">' . EMOL_CVSEARCH_MORE . '</a></div>';
				$text .= '</div>'; //cv-block
			} else {
				$text .= '<div class="emol-no-results">' . get_option( 'emol_cv_no_result' ) . '</div>';
			}


			return $text;

		} else {
			unset( $_SESSION['emol'] );
			eazymatch_trow_error( 'Geen connectie met EazyMatch -> stel eerst een verbinding in via het CMS' );
		}

	}
}
