<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * view a single job [eazymatch view="job"]
 */
class emol_shortcode_job {

	var $job;
	var $jobTexts;
	var $jobCompetences;

	function getContent() {

		global $emol_side;
		global $trailingData;

		//isset in functions.php, by the custom title function
		global $emol_job;

		//emol_dump($emol_job);
		if ( ! isset( $emol_job['job'] ) ) {

			//$jobHtml = (get_option( 'emol_job_offline' )) ? get_option( 'emol_job_offline' ) : 'This page does not exist anymore';
			emol_301( get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_job_search_url' ) . '/all/' );
		} else {

			$this->job            = $emol_job['job'];
			$this->jobTexts       = $emol_job['jobTexts'];
			$this->jobCompetences = $emol_job['jobCompetences'];

			$emol_side = 'applicant';

			//shortcode
			if ( ! isset( $this->job['shortcode'] ) ) {
				$this->job['shortcode'] = 'V' . sprintf( "%05d", $this->job['id'] );
			}

			$class = '';
			$class2 = '';
			if ( ! empty( $this->job['Statusses'] ) ) {
				foreach ( $this->job['Statusses'] as $aroStat ) {
					$class  .= ' emol-job-status-' . $aroStat['jobstatus_id'];
					$class2 .= ' emol-job-status-sub-' . $aroStat['jobstatus_id'];
				}
			}

			$jobHtml = '<div id="emol-job-container" class="' . $class . '">';
			$jobHtml .= '<div class="' . $class2 . '"></div>';
			$jobHtml .= '<h2 class="emol-job-heading">' . $this->job['name'] . '</h2>';

			$jobHtml .= '<div id="emol-job-body">';
			if ( isset( $this->job['Company']['Logo'] ) && $this->job['Company']['Logo']['content'] > '' && get_option( 'emol_job_search_logo' ) == 1 ) {
				$jobHtml .= '<div class="emol-job-picture"><img src="data:image/png;base64,' . $this->job['Company']['Logo']['content'] . '" /></div>';
			}
			//if($this->job['description'] != '') $jobHtml .= '<div id="emol-job-description">'.$this->job['description'].'</div>';
			$jobHtml .= '<table>';

			//code of job
			$jobHtml .= '<tr><td class="emol-job-body-col1">' . EMOL_JOB_CODE . '</td>';
			$jobHtml .= '<td class="emol-job-body-col2">' . $this->job['shortcode'] . '</td></tr>';

			if ( isset( $this->job['Address']['Region']['name'] ) && $this->job['Address']['Region']['name'] != '' ) {
				$addRegion = '';

				if ( isset( $this->job['Address']['Region']['name'] ) ) {
					$addRegion = '' . $this->job['Address']['Region']['name'] . '';
				}
				$jobHtml .= '<tr><td class="emol-job-body-col1">' . EMOL_JOB_PLACE . '</td>';
				//$jobHtml .= '<td>'.$this->job['Address']['city'].' '.$addRegion.'</td></tr>';
				$jobHtml .= '<td>' . $addRegion . '</td></tr>';
			}
			$jdate = '';

			if ( isset( $this->job['startpublished'] ) && ! empty( $this->job['startpublished'] ) ) {
				$jdate = mysql2date( get_option( 'date_format' ), $this->job['startpublished'] );
			} elseif ( isset( $this->job['created'] ) && ! empty( $this->job['created'] ) ) {
				$jdate = mysql2date( get_option( 'date_format' ), $this->job['created'] );
			}

			$jobHtml .= '<tr><td class="emol-job-body-col1">' . EMOL_JOB_DATE . '</td>';
			$jobHtml .= '<td>' . $jdate . '</td></tr>';

			$jobHtml .= '</table></div>';

			$jobHtml .= '<div class="emol-job-apply emol-job-apply-top">
                <a href="' . get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/' . $this->job['id'] . '/' . eazymatch_friendly_seo_string( $this->job['name'] ) . '/" class="emol-button emol-button-apply emol-apply-button" rel="nofollow">' . EMOL_JOB_APPLY . '</a></div>';

			/**
			 * Text blocks
			 *
			 * @var mixed
			 */

			$cust = $this->jobTexts;


			if ( is_array( $cust ) && count( $cust ) > 0 ) {
				foreach ( $cust as $custom ) {
					if ( get_option( 'emol_strip_html' ) == 1 ) {
						$contentText = strip_tags( $custom['value'], '<ul><li><br>' );
					} else {
						$contentText = $custom['value'];
					}
					if ( strlen( $custom['value'] ) == 0 ) {
						continue;
					}

					//check for own title
					$textarea_labels = get_option( 'emol_job_texts' );
					if ( $textarea_labels ) {

						$textarea_labels = unserialize( $textarea_labels );
						//emol_dump($textarea_labels);

						if ( array_key_exists( $custom['title'], $textarea_labels ) ) {
							$custom['title'] = $textarea_labels[ $custom['title'] ];
						}
					}

					$jobHtml .= '<h2 class="emol-job-heading">' . $custom['title'] . '</h2>';
					$jobHtml .= '<p class="emol-job-paragraph">' . emol_markdown::parseLists( $contentText ) . '</p>';
				}
			}

			/**
			 * Add Competences
			 */
			$cHtml = emol_competences::generateTree( $this->jobCompetences );

			if ( $cHtml !== false ) {
				$jobHtml .= '<div class="emol-job-competences">';
				$jobHtml .= '<h3 class="emol-job-heading">' . EMOL_JOB_COMPETENCES . '</h3>';
				$jobHtml .= $cHtml;
				$jobHtml .= '</div>';
			}

			//check manager info / contact part
			$manager_view_check = get_option( 'emol_view_manager_contact' );
			if ( $manager_view_check == 1 ) {
				$manager_options = get_option( 'emol_manager_settings' );
				if ( ! empty( $manager_options ) ) {
					$wordpressManagerSettings = unserialize( get_option( 'emol_manager_settings' ) );

					if ( array_key_exists( $this->job['manager_id'], $wordpressManagerSettings ) ) {
						$manager_settings = $wordpressManagerSettings[ $this->job['manager_id'] ];
						$jobHtml          .= '<div class="emol-manager-contact-table" >';
						$jobHtml          .= '<h3 class="emol-job-heading">' . get_option( 'emol_view_manager_heading' ) . '</h3>';
						// $jobHtml .= '<p class="emol-job-paragraph">';


						if ( ! empty( $manager_settings['photo'] ) ) {
							$jobHtml .= '<div class="emol-manager-photo-container"><img class="emol-manager-picture" src="' . $manager_settings['photo'] . '" ></div>';
						}
						$jobHtml .= '
                        <div class="emol-manager-contact-info" >
                            <div class="emol-manager-name">' . $manager_settings['displayname'] . '</div>
                            <div class="emol-manager-email">' . ( $manager_settings['email'] ) . '</div>
                            <div class="emol-manager-phone">' . $manager_settings['phone'] . '</div>
                            <div class="emol-manager-contact-text">' . nl2br( $manager_settings['text'] ) . '</div>
                        </div>
                        ';

						$jobHtml .= '</div>';
						$jobHtml .= '<div class="emol-manager-bottom"></div>';
						// $jobHtml .= '</p><div class="clear"></div> ';

					}
				}
			}

			$jobHtml .= '<div class="emol-job-apply emol-job-apply-bottom">
                <a href="' . get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/' . $this->job['id'] . '/' . eazymatch_friendly_seo_string( $this->job['name'] ) . '/" class="emol-button emol-button-apply emol-apply-button" rel="nofollow">' . EMOL_JOB_APPLY . '</a></div>';

			$jobHtml .= '</div>'; //job-container

			$sc = get_option( 'emol_sharing_links' );

			if ( $sc != 0 ) {
				//sharethis
				$jobHtml .= '<div class="emol-sharing-section"><div id="emol-share-btns"></div></div> ';
			}

		}

		return $jobHtml;

	}

}

