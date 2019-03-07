<?php

class emol_shortcodehandler {

	/**
	 * Checks for matching shortcodes in the eazymatch plugin
	 *
	 * enables:
	 * [eazymatch view="jobpage"]
	 * [eazymatch view="jobs" competences="31,152"]
	 * [eazymatch view="cv"]
	 * [eazymatch view="apply"]
	 * [eazymatch view="react"]
	 * [eazymatch view="searchjobs" settings="title=search,button=zoek"]
	 *
	 * in contentpages
	 */
	static public function apply( $atts ) {

		extract( shortcode_atts( array(
			'view'        => '',
			'competences' => '',
			'settings'    => ''
		), $atts ) );


		$return = '';

		if ( in_array( $view, array( 'cv', 'job', 'jobs', 'jobpage', 'apply', 'react', 'searchjobs' ) ) ) {

			if ( $view === 'jobs' ) {
				// get the shortcode content for jobs
				// extra params possible here
				$competences = explode( ',', $competences );
				if ( is_array( $competences ) && count( $competences ) > 0 ) {
					foreach ( $competences as $key => $competence ) {
						if ( ! is_numeric( $competence ) ) {
							unset( $competences[ $key ] );
						}
					}
				}

				$shortCodeObj              = new emol_shortcode_jobs();
				$shortCodeObj->competences = $competences;
				$return                    = $shortCodeObj->getContent();
			} else {

				// define shortcode classname
				$shortcodeClass = 'emol_shortcode_' . $view;

				// create new shortcode object
				$shortCodeObj = new $shortcodeClass();

				// get the shortcode content
				$return = $shortCodeObj->getContent( $atts );
			}

			if ( ! empty( $return ) ) {
				// make sure the basic style/scripts are included
				emol_require::all();
			}
		}

		return $return;
	}
}