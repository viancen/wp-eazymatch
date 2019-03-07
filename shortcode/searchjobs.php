<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Get published joblist
 *
 */
class emol_shortcode_searchjobs {

	function getContent( $atts ) {

		if ( empty( $atts['settings'] ) ) {
			return 'Define settings please.';
		}
		$the_settings = explode( '|', $atts['settings'] );

		$settings = array();

		foreach ( $the_settings as $one ) {
			$sub                 = explode( '=', $one );
			$settings[ $sub[0] ] = $sub[1];
		}


		global $emol_side;
		global $trailingData;
		global $emol_api;

		$emol_side = 'applicant';
		$emol_api  = eazymatch_connect();

		if ( $emol_api ) {

			$title = @$settings['title'];

			$reset = @$settings['reset'];

			$searchLabel = @$settings['button'];

			$locationSearchEnabled = true;

			$checkBoxSearch = false;

			$text = $title;

			// type get filters for jobs/applicants
			$filters = emol_jobfilter_factory::createDefault()->getFilterArray();

			$trunk = new EazyTrunk();

			$provinceList = array();

			if ( $emol_side == 'company' ) {
				$competenceList = &$trunk->request( 'competence', 'getPublishedTree', array( false, true ) );
			} else {
				$competenceList = &$trunk->request( 'job', 'getPublishedCompetenceTree', array( $filters ) );

				if ( $locationSearchEnabled ) {
					$provinceList = &$trunk->request( 'job', 'getPublishedProvinces', array( $filters ) );
				}
			}

			// execute the trunk request
			$trunk->execute();

			$lists = array();

			if ( $emol_side == 'company' ) {
				$setUrl = get_option( 'emol_cv_search_url' );
			} else {
				$setUrl = get_option( 'emol_job_search_url' );
			}

			if ( count( $competenceList ) > 0 ) {
				if ( $checkBoxSearch == 1 ) {
					$lists = new emol_Level2Checkboxes( $competenceList, $setUrl, 'search_competences_checkboxes' );
				} else {
					$lists = new emol_Level2Listboxes( $competenceList, $setUrl, 'search_competences' );
				}
			} else {
				$lists = array();
			}

			$text .= '<div class="emol_widget" id="emol_search_widget">';

			//check multi slugs
			$completeBase = explode( '/', get_bloginfo( 'wpurl' ) );
			if ( count( $completeBase ) > 3 ) {
				$setUrl = array_pop( $completeBase ) . '/' . $setUrl;
			}

			$text .= '<form onsubmit="emolSearch(\'' . $setUrl . '\'); return false;">';

			$text .= '<div class="emol-free-search">
            <label for="emol-free-search-input">' . EMOL_WIDGET_FREE_SEARCH . '</label>
            <div id="emol-free-input">
                    <input type="text" value="' . urldecode( emol_session::get( 'freeSearch' ) ) . '" class="emol-text-input noautosubmit" name="emol-free-search" id="emol-free-search-input" />
            </div>
        </div>';

			if ( $locationSearchEnabled == true ) {
				//checked values
				$val5  = '';
				$val10 = '';
				$val15 = '';
				$val25 = '';
				$val50 = '';

				//range
				switch ( urldecode( emol_session::get( 'locationSearchRange' ) ) ) {
					case '5':
						$val5 = 'selected="selected"';
						break;
					case '10':
						$val10 = 'selected="selected"';
						break;
					case '15':
						$val15 = 'selected="selected"';
						break;
					case '25':
						$val25 = 'selected="selected"';
						break;
					case '50':
						$val50 = 'selected="selected"';
						break;
				}

				//selectbox for range
				$rangeBox = '<select class="emol-text-input" name="emol-range-search" id="emol-range-search-input">
                <option value="5" ' . $val5 . '>5 ' . EMOL_KM . '</option>
                <option value="10" ' . $val10 . '>10 ' . EMOL_KM . '</option>
                <option value="15" ' . $val15 . '>15 ' . EMOL_KM . '</option>
                <option value="25" ' . $val25 . '>25 ' . EMOL_KM . '</option>
                <option value="50" ' . $val50 . '>50 ' . EMOL_KM . '</option>
                </select>';

				$text .= '<div class="emol-location-search">
                    <label for="emol-zipcode-search-input">' . EMOL_WIDGET_LOCATION_SEARCH . '</label>
                        <div id="emol-location-input">
                        <input type="text" value="' . urldecode( emol_session::get( 'locationSearchZipcode' ) ) . '" class="emol-text-input" name="emol-zipcode-search" id="emol-zipcode-search-input" />
                        ' . $rangeBox . '
                        </div>
                    </div>';

				if ( count( $provinceList ) > 0 ) {
					$currentProvince = emol_session::get( 'locationSearchProvince' );

					$text .= '<div class="emol-province-search">';
					$text .= '<label for="emol-province-search-input">' . EMOL_WIDGET_PROVINCE_SEARCH . '</label>';
					$text .= '<div id="emol-province-input">';
					$text .= '<select class="emol-province-search-input" name="emol-province-search-input" id="emol-province-search-input">';
					$text .= '<option></option>';

					foreach ( $provinceList as $province ) {
						$selected = $currentProvince == $province['id'] ? ' selected="selected"' : '';

						$text .= '<option value="' . $province['id'] . '"' . $selected . '>' . $province['name'] . '</option>';
					}

					$text .= '</select>';
					$text .= ' </div></div>';
				}
			}
			if ( isset( $lists->lists ) && ! is_array( $lists->lists ) ) {
				$text .= $lists->lists;
			}

			//$allUrl = str_replace('//','/',$setUrl.'/'.get_option( 'emol_job_search_url' ));
			$text .= '
            <div class="emol-submit-wrapper">
                <span class="emol-reset-button"><a href="' . get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_job_search_url' ) . '/all/" class="emol-altbutton emol-button-reset">' . $reset . '</a></span>
                <button onclick="emolSearch(\'/' . $setUrl . '/\');" class="emol-button emol-button-search">' . $searchLabel . '</button>
            </div>';

			$text .= "</form>";

			$text .= "</div>";


			return ( $text );

		} else {
			unset( $_SESSION['emol'] );
			eazymatch_trow_error( 'Geen connectie met EazyMatch -> stel eerst een verbinding in via het CMS' );
		}

	}
}

