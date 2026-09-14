<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Resolves the "job-teaser-text" shortcode attribute for job overviews.
 *
 * The attribute points to one of the text blocks that are defined for jobs in
 * EazyMatch (form.getJobTextDescription). The value of that block per job comes
 * from job.getCustomTexts, which only carries title + value. So:
 *
 *   attribute (id or title)  --getJobTextDescription-->  label/title
 *   label/title              --getCustomTexts(job)-->     value
 *
 * All API traffic goes through a single emol_trunk batch.
 */
class emol_jobteaser {

	/**
	 * Field in a form.getJobTextDescription item that holds the stable id.
	 */
	const ID_FIELD = 'id';

	/**
	 * Field in a form.getJobTextDescription item that holds the title as it
	 * is returned by job.getCustomTexts.
	 */
	const LABEL_FIELD = 'label';

	/**
	 * Reads the attribute value from a shortcode attribute array.
	 *
	 * @param array $atts
	 *
	 * @return string  empty string when not set
	 */
	static public function fromAtts( $atts ) {
		if ( ! is_array( $atts ) ) {
			return '';
		}

		$name = emol_shortcoderegistry::ATTR_JOB_TEASER_TEXT;

		// WordPress lowercases attribute names, but be lenient about the separator
		foreach ( array( $name, str_replace( '-', '_', $name ) ) as $candidate ) {
			if ( isset( $atts[ $candidate ] ) && trim( (string) $atts[ $candidate ] ) !== '' ) {
				return trim( (string) $atts[ $candidate ] );
			}
		}

		return '';
	}

	/**
	 * Translates the attribute value to the title used in job.getCustomTexts.
	 *
	 * Numeric   -> looked up by id in the text block definitions
	 * Otherwise -> used as title directly
	 *
	 * @param string $reference   value of the shortcode attribute
	 * @param array  $definitions result of form.getJobTextDescription
	 *
	 * @return string|null  null when an id was given that does not exist
	 */
	static public function resolveTitle( $reference, $definitions ) {
		$reference = trim( (string) $reference );

		if ( $reference === '' ) {
			return null;
		}

		if ( ! is_numeric( $reference ) ) {
			return $reference;
		}

		if ( ! is_array( $definitions ) ) {
			return null;
		}

		foreach ( $definitions as $definition ) {
			if ( ! is_array( $definition ) || ! isset( $definition[ self::ID_FIELD ] ) ) {
				continue;
			}

			if ( (string) $definition[ self::ID_FIELD ] === (string) $reference ) {
				return isset( $definition[ self::LABEL_FIELD ] ) ? (string) $definition[ self::LABEL_FIELD ] : null;
			}
		}

		return null;
	}

	/**
	 * Picks the value of the text block with the given title from the custom
	 * texts of one job.
	 *
	 * @param string $title
	 * @param array  $customTexts result of job.getCustomTexts for one job
	 *
	 * @return string|null  null when missing or empty
	 */
	static public function pickText( $title, $customTexts ) {
		if ( $title === null || $title === '' || ! is_array( $customTexts ) ) {
			return null;
		}

		$wanted = self::normalizeTitle( $title );

		foreach ( $customTexts as $customText ) {
			if ( ! is_array( $customText ) || ! isset( $customText['title'] ) ) {
				continue;
			}

			if ( self::normalizeTitle( $customText['title'] ) !== $wanted ) {
				continue;
			}

			$value = isset( $customText['value'] ) ? (string) $customText['value'] : '';
			if ( trim( strip_tags( $value ) ) === '' ) {
				return null;
			}

			return $value;
		}

		return null;
	}

	/**
	 * Fetches the teaser text for a list of jobs in one API round-trip.
	 *
	 * @param array  $jobs      job arrays as returned by getPublished / searchPublished (need an 'id')
	 * @param string $reference value of the shortcode attribute
	 *
	 * @return array  job id => teaser text; jobs without a usable text are absent
	 */
	static public function fetchForJobs( $jobs, $reference ) {
		$reference = trim( (string) $reference );

		if ( $reference === '' || ! is_array( $jobs ) || count( $jobs ) === 0 ) {
			return array();
		}

		$trunk = new emol_trunk();

		$definitions = &$trunk->request( 'form', 'getJobTextDescription' );

		$textRequests = array();
		foreach ( $jobs as $job ) {
			if ( ! is_array( $job ) || empty( $job['id'] ) ) {
				continue;
			}
			$textRequests[ $job['id'] ] = &$trunk->request( 'job', 'getCustomTexts', array( $job['id'] ) );
		}

		if ( count( $textRequests ) === 0 ) {
			return array();
		}

		$trunk->execute();

		return self::map( $reference, $definitions, $textRequests );
	}

	/**
	 * Pure mapping step, separated from fetchForJobs so it can be tested
	 * without an API connection.
	 *
	 * @param string $reference
	 * @param array  $definitions   result of form.getJobTextDescription
	 * @param array  $textsPerJob   job id => result of job.getCustomTexts
	 *
	 * @return array  job id => teaser text
	 */
	static public function map( $reference, $definitions, $textsPerJob ) {
		$title = self::resolveTitle( $reference, $definitions );

		if ( $title === null ) {
			return array();
		}

		$result = array();
		foreach ( $textsPerJob as $jobId => $customTexts ) {
			$text = self::pickText( $title, $customTexts );
			if ( $text !== null ) {
				$result[ $jobId ] = $text;
			}
		}

		return $result;
	}

	/**
	 * @param string $title
	 *
	 * @return string
	 */
	static private function normalizeTitle( $title ) {
		$title = trim( (string) $title );

		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $title, 'UTF-8' ) : strtolower( $title );
	}
}
