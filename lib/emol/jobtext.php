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
 * Missing option, missing block or missing context means "show" (default on).
 */
class emol_jobtext {

	const OPTION         = 'emol_job_text_display';
	const CONTEXT_DETAIL = 'detail';
	const CONTEXT_LIST   = 'list';

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public static function displayMap() {
		return self::normalizeMap( get_option( self::OPTION ) );
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
			return true;
		}

		$wanted = self::normalizeTitle( $title );

		foreach ( $map as $label => $flags ) {
			if ( self::normalizeTitle( $label ) !== $wanted ) {
				continue;
			}

			if ( ! is_array( $flags ) || ! array_key_exists( $context, $flags ) ) {
				return true;
			}

			return ! empty( $flags[ $context ] );
		}

		return true;
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
			return true;
		}

		foreach ( $map as $flags ) {
			if ( ! is_array( $flags ) || ! array_key_exists( $context, $flags ) || ! empty( $flags[ $context ] ) ) {
				return true;
			}
		}

		return false;
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
			if ( ! is_array( $block ) || ! isset( $block['title'] ) ) {
				continue;
			}

			if ( ! self::isVisible( $block['title'], $context, $map ) ) {
				continue;
			}

			$value = isset( $block['value'] ) ? (string) $block['value'] : '';
			if ( trim( strip_tags( $value ) ) === '' ) {
				continue;
			}

			$result[] = array(
				'title' => (string) $block['title'],
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
	private static function normalizeTitle( $title ) {
		$title = trim( (string) $title );

		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $title, 'UTF-8' ) : strtolower( $title );
	}
}
