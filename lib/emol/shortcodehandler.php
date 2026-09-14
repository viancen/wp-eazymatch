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
	 * [eazymatch view="jobs" job-teaser-text="12"]
	 *
	 * in contentpages
	 *
	 * The complete list of views and parameters lives in emol_shortcoderegistry
	 * (and is shown in the CMS under EazyMatch > Shortcodes).
	 */
	static public function apply( $atts ) {

		$shortcodeSettings = shortcode_atts( array(
			'view'        => '',
			'competences' => '',
			'settings'    => ''
		), $atts );
		$view = $shortcodeSettings['view'];
		$competences = $shortcodeSettings['competences'];

		$return = '';

		if ( emol_shortcoderegistry::isView( $view ) ) {

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
				$return                    = $shortCodeObj->getContent( $atts );

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
