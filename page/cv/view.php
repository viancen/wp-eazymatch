<?php

/**
 * Container for cv view
 */
class emol_page_cv_view extends emol_page {
	/**
	 * When initialized this will be the handled cv
	 *
	 * @var mixed
	 */
	var $cv;

	/**
	 * When initialized this will be the handled cv competences
	 *
	 * @var mixed
	 */
	var $cvCompetences;

	protected function preparePost() {
		//split up the variables given
		$urlVars = explode( '/', $this->page_slug );
		$cvId    = $urlVars[1];

		//fetch the cv
		eazymatch_connect();
		$trunk = new EazyTrunk();

		// create a response array and add all the requests to the trunk
		$this->cv            = &$trunk->request( 'applicant', 'getPublishedSummary', array( $cvId ) );
		$this->cvCompetences = &$trunk->request( 'applicant', 'getPublishedCompetence', array( $cvId ) );

		// execute the trunk request
		$trunk->execute();


		//set the page variables
		$this->page_title = EMOL_CV_NAME . ' - ' . $this->cv['title'];

		/**
		 * We'll wait til WordPress has looked for posts, and then
		 * check to see if the requested url matches our target.
		 */
		add_filter( 'the_posts', array( &$this, 'detectPost' ) );
	}

	/**
	 * gets the content requested
	 *
	 * no params expected
	 */
	function getContent() {

		//check login?
		if ( get_option( 'emol_cv_secure' ) == 1 && ! ( emol_session::isContact() ) ) {
			$errorMesssage = '<div id="emol-error-no-access-cv-database"><a href="/' . get_option( 'emol_react_url_cv' ) . '/0/open">' . get_option( 'emol_react_cv_error_secure' ) . '</a></div>';

			return $errorMesssage;
		}

		$img = '';
		if ( isset( $this->cv['Person']['Picture'] ) && $this->cv['Person']['Picture']['content'] > '' && get_option( 'emol_cv_search_picture' ) == 1 ) {
			$img = '<div class="emol-cv-result-picture"><img src="data:image/png;base64,' . $this->cv['Person']['Picture']['content'] . '" /></div>';
		}

		if ( ! isset( $this->cv['Person']['shortcode'] ) ) {
			$this->cv['Person']['shortcode'] = 'A' . $this->cv['id'];
		}

		//emol_debug($this->cv);
		//$jobHtml = '<h2 class="emol-job-heading">'.$this->job['name'].'</h2>';
		$cvHtml = '<div id="emol-cv-container">';
		$cvHtml .= '<span class="emol-cv-slogan">' . $this->cv['Person']['shortcode'] . '</span>';
		$cvHtml .= '<div id="emol-cv-body">';
		$cvHtml .= '<table>';
		if ( $img != '' ) {
			$cvHtml .= '<tr><td class="emol-cv-body-col1">' . EMOL_CV_PICTURE . '</td>';
			$cvHtml .= '<td class="emol-cv-body-col2">' . $img . '</td></tr>';
		}

		if ( isset( $this->cv['Person']['Preferedaddress'] ) && ! empty( $this->cv['Person']['Preferedaddress']['city'] ) ) {
			$cvHtml .= '<tr><td class="emol-cv-body-col1">' . EMOL_CV_PLACE . '</td>';
			$cvHtml .= '<td>' . $this->cv['Person']['Preferedaddress']['city'] . '</td></tr>';
		}

		$gender = EMOL_MALE;
		if ( $this->cv['Person']['gender'] == 'f' ) {
			$gender = EMOL_FEMALE;
		}

		$cvHtml .= '<tr><td class="emol-cv-body-col1">' . EMOL_GENDER . '</td>';
		$cvHtml .= '<td>' . $gender . '</td></tr>';

		$bdate = '-';
		if ( ! empty( $this->cv['Person']['birthdate'] ) ) {
			$bdate = emol_getAge( $this->cv['Person']['birthdate'] );

			$cvHtml .= '<tr><td class="emol-cv-body-col1">' . EMOL_CV_AGE . '</td>';
			$cvHtml .= '<td>' . $bdate . '</td></tr>';
		}

		$bpdate = EMOL_CV_DIRECT;
		if ( $this->cv['availablefrom'] ) {
			$bpdate = date( 'd-m-Y', strtotime( $this->cv['availablefrom'] ) );
		}

		$bpdate = EMOL_CV_DIRECT;

		if ( isset( $this->cv['availablefrom'] ) && ! empty( $this->cv['availablefrom'] ) ) {
			$bpdate = mysql2date( get_option( 'date_format' ), $this->cv['availablefrom'] );
		}


		$cvHtml .= '<tr><td class="emol-cv-body-col1">' . EMOL_CV_AVAILABLEFROM . '</td>';
		$cvHtml .= '<td>' . $bpdate . '</td></tr>';


		$cvHtml .= '</table>';
		$cvHtml .= '</div>';


		if ( isset( $this->cv['description'] ) && ! empty( $this->cv['description'] ) ) {
			$cvHtml .= '<h2 class="emol-cv-heading">' . EMOL_CV_DESCRIPTION . '</h2>';
			$cvHtml .= '<p class="emol-cv-paragraph">' . $this->cv['description'] . '</p>';
		}

		/**
		 * Add Competences
		 */
		$cHtml = emol_competences::generateTree( $this->cvCompetences );

		if ( $cHtml !== false ) {
			$cvHtml .= '<div class="emol-cv-competences">';
			$cvHtml .= '<h3 class="emol-cv-heading">' . EMOL_CV_MATCHPROFILE . '</h3>';
			$cvHtml .= $cHtml;
			$cvHtml .= '</div>';
		}


		//finalize the html
		$cvHtml .= '
		<div class="emol-cv-apply">
			<a href="' . get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_react_url_cv' ) . '/' . $this->cv['id'] . '/' . eazymatch_friendly_seo_string( $this->cv['title'] ) . '/" class="emol-button emol-button-apply emol-apply-button">' . EMOL_CVSEARCH_APPLY . '</a>
		</div>';
		$cvHtml .= '</div>'; //job-container

		return $cvHtml;
	}
}