<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Visibility of job text blocks on the detail page and on job overviews.
 *
 * Stored as option emol_job_text_display, keyed by the original API label:
 *
 *     array( 'Functieomschrijving' => array( 'detail' => 1, 'list' => 0 ) )
 *
 * Defaults when nothing is saved yet:
 * - detail page: text blocks on
 * - overview: text blocks off, meta description on
 */
class emol_jobtext {

	const OPTION         = 'emol_job_text_display';
	const DESC_OPTION    = 'emol_job_description_display';
	const CONTEXT_DETAIL = 'detail';
	const CONTEXT_LIST   = 'list';

	/**
	 * Default visibility when a flag was never stored.
	 *
	 * @param string $context
	 *
	 * @return bool
	 */
	public static function defaultOn( $context ) {
		return $context === self::CONTEXT_DETAIL;
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public static function displayMap() {
		$map = self::normalizeMap( get_option( self::OPTION ) );

		if ( self::allFlagsOff( $map ) ) {
			return array();
		}

		return $map;
	}

	/**
	 * All-zero maps come from the old hidden+checkbox save bug; treat as unset.
	 *
	 * @param array $map
	 *
	 * @return bool
	 */
	public static function allFlagsOff( $map ) {
		if ( ! is_array( $map ) || count( $map ) === 0 ) {
			return false;
		}

		foreach ( $map as $flags ) {
			if ( ! is_array( $flags ) ) {
				continue;
			}
			if ( self::flagIsOn( isset( $flags['detail'] ) ? $flags['detail'] : 0 ) ) {
				return false;
			}
			if ( self::flagIsOn( isset( $flags['list'] ) ? $flags['list'] : 0 ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param mixed $raw Option value (array, serialized string, or empty).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function normalizeMap( $raw ) {
		if ( is_string( $raw ) && $raw !== '' ) {
			$decoded = @unserialize( $raw );
			if ( is_array( $decoded ) ) {
				$raw = $decoded;
			}
		}

		return is_array( $raw ) ? $raw : array();
	}

	/**
	 * @param string $title   Original API title (before label remap).
	 * @param string $context self::CONTEXT_DETAIL or self::CONTEXT_LIST.
	 * @param array  $map     Optional preloaded map; defaults to the saved option.
	 *
	 * @return bool
	 */
	public static function isVisible( $title, $context, $map = null ) {
		if ( $map === null ) {
			$map = self::displayMap();
		}

		if ( ! is_array( $map ) || count( $map ) === 0 ) {
			return self::defaultOn( $context );
		}

		$wanted = self::normalizeTitle( $title );

		foreach ( $map as $label => $flags ) {
			if ( self::normalizeTitle( $label ) !== $wanted ) {
				continue;
			}

			if ( ! is_array( $flags ) || ! array_key_exists( $context, $flags ) ) {
				return self::defaultOn( $context );
			}

			return self::flagIsOn( $flags[ $context ] );
		}

		return self::defaultOn( $context );
	}

	/**
	 * Whether at least one text block should appear on overviews.
	 *
	 * @param array|null $map
	 *
	 * @return bool
	 */
	public static function hasVisibleIn( $context, $map = null ) {
		if ( $map === null ) {
			$map = self::displayMap();
		}

		if ( ! is_array( $map ) || count( $map ) === 0 ) {
			return self::defaultOn( $context );
		}

		foreach ( $map as $label => $flags ) {
			if ( self::isVisible( $label, $context, $map ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Visibility of the separate job meta description (not a text block).
	 *
	 * Overview defaults on; detail defaults off unless explicitly enabled
	 * or the legacy emol_job_search_desc switch was on.
	 *
	 * @param string $context
	 *
	 * @return bool
	 */
	public static function descriptionVisible( $context ) {
		$saved = get_option( self::DESC_OPTION );
		if ( is_string( $saved ) && $saved !== '' ) {
			$decoded = @unserialize( $saved );
			if ( is_array( $decoded ) ) {
				$saved = $decoded;
			}
		}

		if ( is_array( $saved ) && ( array_key_exists( 'detail', $saved ) || array_key_exists( 'list', $saved ) ) ) {
			if ( ! array_key_exists( $context, $saved ) ) {
				return $context === self::CONTEXT_LIST;
			}

			return self::flagIsOn( $saved[ $context ] );
		}

		$legacy = get_option( 'emol_job_search_desc' );
		if ( $legacy === '1' || $legacy === 1 ) {
			return true;
		}
		if ( $legacy === '0' || $legacy === 0 ) {
			return false;
		}

		return $context === self::CONTEXT_LIST;
	}

	/**
	 * Saved meta-description flags for the admin checkboxes.
	 *
	 * @param mixed $raw
	 *
	 * @return array{detail:bool,list:bool}
	 */
	public static function descriptionFlags( $raw = null ) {
		if ( $raw === null ) {
			$raw = get_option( self::DESC_OPTION );
		}
		if ( is_string( $raw ) && $raw !== '' ) {
			$decoded = @unserialize( $raw );
			if ( is_array( $decoded ) ) {
				$raw = $decoded;
			}
		}

		if ( is_array( $raw ) && ( array_key_exists( 'detail', $raw ) || array_key_exists( 'list', $raw ) ) ) {
			return array(
				'detail' => self::flagIsOn( isset( $raw['detail'] ) ? $raw['detail'] : 0 ),
				'list'   => ! array_key_exists( 'list', $raw ) || self::flagIsOn( $raw['list'] ),
			);
		}

		$legacy = get_option( 'emol_job_search_desc' );
		if ( $legacy === '1' || $legacy === 1 ) {
			return array( 'detail' => true, 'list' => true );
		}
		if ( $legacy === '0' || $legacy === 0 ) {
			return array( 'detail' => false, 'list' => false );
		}

		return array( 'detail' => false, 'list' => true );
	}

	/**
	 * Filter custom texts to the blocks visible in a context.
	 *
	 * @param mixed  $blocks
	 * @param string $context
	 * @param array  $map
	 *
	 * @return array<int, array{title:string,value:string}>
	 */
	public static function filterBlocks( $blocks, $context, $map = null ) {
		$result = array();

		if ( ! is_array( $blocks ) ) {
			return $result;
		}

		if ( $map === null ) {
			$map = self::displayMap();
		}

		foreach ( $blocks as $block ) {
			if ( ! is_array( $block ) ) {
				continue;
			}

			$title = self::blockTitle( $block );
			if ( $title === '' || ! self::isVisible( $title, $context, $map ) ) {
				continue;
			}

			$value = isset( $block['value'] ) ? (string) $block['value'] : '';
			if ( trim( strip_tags( $value ) ) === '' ) {
				continue;
			}

			$result[] = array(
				'title' => $title,
				'value' => $value,
			);
		}

		return $result;
	}

	/**
	 * Batch-load custom texts (and text-block definitions) for a list of jobs.
	 *
	 * @param array $jobs
	 *
	 * @return array{definitions:array,texts:array<int|string,mixed>}
	 */
	public static function fetchBundle( $jobs ) {
		$empty = array(
			'definitions' => array(),
			'texts'       => array(),
		);

		if ( ! is_array( $jobs ) || count( $jobs ) === 0 ) {
			return $empty;
		}

		$trunk       = new emol_trunk();
		$definitions = &$trunk->request( 'form', 'getJobTextDescription' );
		$texts       = array();

		foreach ( $jobs as $job ) {
			if ( ! is_array( $job ) || empty( $job['id'] ) ) {
				continue;
			}
			$texts[ $job['id'] ] = &$trunk->request( 'job', 'getCustomTexts', array( $job['id'] ) );
		}

		if ( count( $texts ) === 0 ) {
			return $empty;
		}

		$trunk->execute();

		return array(
			'definitions' => is_array( $definitions ) ? $definitions : array(),
			'texts'       => $texts,
		);
	}

	/**
	 * @param array  $textsPerJob job id => getCustomTexts
	 * @param string $context
	 * @param array  $map
	 *
	 * @return array<int|string, array<int, array{title:string,value:string}>>
	 */
	public static function filterAll( $textsPerJob, $context, $map = null ) {
		$result = array();

		if ( ! is_array( $textsPerJob ) ) {
			return $result;
		}

		if ( $map === null ) {
			$map = self::displayMap();
		}

		foreach ( $textsPerJob as $jobId => $blocks ) {
			$filtered = self::filterBlocks( $blocks, $context, $map );
			if ( count( $filtered ) > 0 ) {
				$result[ $jobId ] = $filtered;
			}
		}

		return $result;
	}

	/**
	 * CSS that keeps plugin job content visible on every theme.
	 *
	 * @return string
	 */
	public static function frontCss() {
		return '#emol-job-container .emol-job-textblocks,'
			. '#emol-job-container .emol-job-textblock,'
			. '#emol-job-container .emol-job-textblock-heading,'
			. '#emol-job-container .emol-job-textblock-body,'
			. '#emol-job-container .emol-job-meta-description{display:block!important;visibility:visible!important;height:auto!important;max-height:none!important;overflow:visible!important;opacity:1!important}';
	}

	/**
	 * Theme-independent HTML for the optional meta description.
	 *
	 * @param string $description
	 *
	 * @return string
	 */
	public static function renderDescriptionHtml( $description ) {
		$description = trim( (string) $description );
		if ( $description === '' ) {
			return '';
		}

		return '<div class="emol-job-meta-description" style="display:block!important;visibility:visible!important;margin:0 0 1em">'
			. emol_firstWords( $description )
			. '</div>';
	}

	/**
	 * Theme-independent HTML for job text blocks.
	 *
	 * Headings are never a direct child of #emol-job-container and never use
	 * emol-job-heading, so theme rules that hide the duplicate job title
	 * cannot hide these blocks. Inline CSS keeps them visible even when a
	 * cached plugin stylesheet still hides older markup.
	 *
	 * @param mixed  $blocks
	 * @param string $context
	 *
	 * @return string
	 */
	public static function renderHtml( $blocks, $context ) {
		$filtered = self::filterBlocks( $blocks, $context );
		if ( count( $filtered ) === 0 ) {
			return '';
		}

		$strip = (string) get_option( 'emol_strip_html' ) === '1';
		$html  = '<div class="emol-job-textblocks" style="display:block!important;visibility:visible!important;clear:both;margin:1.5em 0">';
		$html .= '<style type="text/css">' . self::frontCss() . '</style>';

		foreach ( $filtered as $block ) {
			$value = $block['value'];
			if ( $strip ) {
				$value = strip_tags( $value, '<ul><li><br><p><strong><em><ol><img>' );
			}

			$html .= '<div class="emol-job-textblock" style="display:block!important;margin:0 0 1.5em">';
			$html .= '<h2 class="emol-job-textblock-heading" style="display:block!important;visibility:visible!important;font-size:1.2em;font-weight:700;margin:0 0 .4em">'
				. self::remapTitle( $block['title'] )
				. '</h2>';
			$html .= '<div class="emol-job-paragraph emol-job-textblock-body" style="display:block!important;visibility:visible!important">'
				. emol_markdown::parseLists( nl2br( $value ) )
				. '</div>';
			$html .= '</div>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Title field as returned by job.getCustomTexts.
	 *
	 * @param array $block
	 *
	 * @return string
	 */
	public static function blockTitle( $block ) {
		foreach ( array( 'title', 'label', 'name' ) as $key ) {
			if ( isset( $block[ $key ] ) && trim( (string) $block[ $key ] ) !== '' ) {
				return (string) $block[ $key ];
			}
		}

		return '';
	}

	/**
	 * Apply the admin label override from emol_job_texts.
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public static function remapTitle( $title ) {
		$labels = get_option( 'emol_job_texts' );
		if ( is_string( $labels ) && $labels !== '' ) {
			$decoded = @unserialize( $labels );
			if ( is_array( $decoded ) ) {
				$labels = $decoded;
			}
		}

		if ( is_array( $labels ) && array_key_exists( $title, $labels ) && (string) $labels[ $title ] !== '' ) {
			return (string) $labels[ $title ];
		}

		return $title;
	}

	/**
	 * @param string $title
	 *
	 * @return string
	 */
	/**
	 * @param mixed $value Posted or stored flag.
	 *
	 * @return bool
	 */
	public static function flagIsOn( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $item ) {
				if ( self::flagIsOn( $item ) ) {
					return true;
				}
			}

			return false;
		}

		return $value === 1 || $value === '1' || $value === true || $value === 'true';
	}

	/**
	 * @param string $title
	 *
	 * @return string
	 */
	private static function normalizeTitle( $title ) {
		$title = trim( (string) $title );

		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $title, 'UTF-8' ) : strtolower( $title );
	}
}
