<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}
/**
 * Creates a seo friendly string
 *
 * @param mixed $input
 *
 * @return mixed
 */
function eazymatch_friendly_seo_string( $input, $options = array() ) {
	// Make sure string is in UTF-8 and strip invalid UTF-8 characters
	$str = mb_convert_encoding( (string) $input, 'UTF-8', mb_list_encodings() );

	$defaults = array(
		'delimiter'     => '-',
		'limit'         => null,
		'lowercase'     => true,
		'replacements'  => array(),
		'transliterate' => false,
	);

	// Merge options
	$options = array_merge( $defaults, $options );

	$char_map = array(
		// Latin
		'À' => 'A',
		'Á' => 'A',
		'Â' => 'A',
		'Ã' => 'A',
		'Ä' => 'A',
		'Å' => 'A',
		'Æ' => 'AE',
		'Ç' => 'C',
		'È' => 'E',
		'É' => 'E',
		'Ê' => 'E',
		'Ë' => 'E',
		'Ì' => 'I',
		'Í' => 'I',
		'Î' => 'I',
		'Ï' => 'I',
		'Ð' => 'D',
		'Ñ' => 'N',
		'Ò' => 'O',
		'Ó' => 'O',
		'Ô' => 'O',
		'Õ' => 'O',
		'Ö' => 'O',
		'Ő' => 'O',
		'Ø' => 'O',
		'Ù' => 'U',
		'Ú' => 'U',
		'Û' => 'U',
		'Ü' => 'U',
		'Ű' => 'U',
		'Ý' => 'Y',
		'Þ' => 'TH',
		'ß' => 'ss',
		'à' => 'a',
		'á' => 'a',
		'â' => 'a',
		'ã' => 'a',
		'ä' => 'a',
		'å' => 'a',
		'æ' => 'ae',
		'ç' => 'c',
		'è' => 'e',
		'é' => 'e',
		'ê' => 'e',
		'ë' => 'e',
		'ì' => 'i',
		'í' => 'i',
		'î' => 'i',
		'ï' => 'i',
		'ð' => 'd',
		'ñ' => 'n',
		'ò' => 'o',
		'ó' => 'o',
		'ô' => 'o',
		'õ' => 'o',
		'ö' => 'o',
		'ő' => 'o',
		'ø' => 'o',
		'ù' => 'u',
		'ú' => 'u',
		'û' => 'u',
		'ü' => 'u',
		'ű' => 'u',
		'ý' => 'y',
		'þ' => 'th',
		'ÿ' => 'y',
		// Latin symbols
		'©' => '(c)',
		// Greek
		'Α' => 'A',
		'Β' => 'B',
		'Γ' => 'G',
		'Δ' => 'D',
		'Ε' => 'E',
		'Ζ' => 'Z',
		'Η' => 'H',
		'Θ' => '8',
		'Ι' => 'I',
		'Κ' => 'K',
		'Λ' => 'L',
		'Μ' => 'M',
		'Ν' => 'N',
		'Ξ' => '3',
		'Ο' => 'O',
		'Π' => 'P',
		'Ρ' => 'R',
		'Σ' => 'S',
		'Τ' => 'T',
		'Υ' => 'Y',
		'Φ' => 'F',
		'Χ' => 'X',
		'Ψ' => 'PS',
		'Ω' => 'W',
		'Ά' => 'A',
		'Έ' => 'E',
		'Ί' => 'I',
		'Ό' => 'O',
		'Ύ' => 'Y',
		'Ή' => 'H',
		'Ώ' => 'W',
		'Ϊ' => 'I',
		'Ϋ' => 'Y',
		'α' => 'a',
		'β' => 'b',
		'γ' => 'g',
		'δ' => 'd',
		'ε' => 'e',
		'ζ' => 'z',
		'η' => 'h',
		'θ' => '8',
		'ι' => 'i',
		'κ' => 'k',
		'λ' => 'l',
		'μ' => 'm',
		'ν' => 'n',
		'ξ' => '3',
		'ο' => 'o',
		'π' => 'p',
		'ρ' => 'r',
		'σ' => 's',
		'τ' => 't',
		'υ' => 'y',
		'φ' => 'f',
		'χ' => 'x',
		'ψ' => 'ps',
		'ω' => 'w',
		'ά' => 'a',
		'έ' => 'e',
		'ί' => 'i',
		'ό' => 'o',
		'ύ' => 'y',
		'ή' => 'h',
		'ώ' => 'w',
		'ς' => 's',
		'ϊ' => 'i',
		'ΰ' => 'y',
		'ϋ' => 'y',
		'ΐ' => 'i',
		// Turkish
		'Ş' => 'S',
		'İ' => 'I',
		'Ç' => 'C',
		'Ü' => 'U',
		'Ö' => 'O',
		'Ğ' => 'G',
		'ş' => 's',
		'ı' => 'i',
		'ç' => 'c',
		'ü' => 'u',
		'ö' => 'o',
		'ğ' => 'g',
		// Russian
		'А' => 'A',
		'Б' => 'B',
		'В' => 'V',
		'Г' => 'G',
		'Д' => 'D',
		'Е' => 'E',
		'Ё' => 'Yo',
		'Ж' => 'Zh',
		'З' => 'Z',
		'И' => 'I',
		'Й' => 'J',
		'К' => 'K',
		'Л' => 'L',
		'М' => 'M',
		'Н' => 'N',
		'О' => 'O',
		'П' => 'P',
		'Р' => 'R',
		'С' => 'S',
		'Т' => 'T',
		'У' => 'U',
		'Ф' => 'F',
		'Х' => 'H',
		'Ц' => 'C',
		'Ч' => 'Ch',
		'Ш' => 'Sh',
		'Щ' => 'Sh',
		'Ъ' => '',
		'Ы' => 'Y',
		'Ь' => '',
		'Э' => 'E',
		'Ю' => 'Yu',
		'Я' => 'Ya',
		'а' => 'a',
		'б' => 'b',
		'в' => 'v',
		'г' => 'g',
		'д' => 'd',
		'е' => 'e',
		'ё' => 'yo',
		'ж' => 'zh',
		'з' => 'z',
		'и' => 'i',
		'й' => 'j',
		'к' => 'k',
		'л' => 'l',
		'м' => 'm',
		'н' => 'n',
		'о' => 'o',
		'п' => 'p',
		'р' => 'r',
		'с' => 's',
		'т' => 't',
		'у' => 'u',
		'ф' => 'f',
		'х' => 'h',
		'ц' => 'c',
		'ч' => 'ch',
		'ш' => 'sh',
		'щ' => 'sh',
		'ъ' => '',
		'ы' => 'y',
		'ь' => '',
		'э' => 'e',
		'ю' => 'yu',
		'я' => 'ya',
		// Ukrainian
		'Є' => 'Ye',
		'І' => 'I',
		'Ї' => 'Yi',
		'Ґ' => 'G',
		'є' => 'ye',
		'і' => 'i',
		'ї' => 'yi',
		'ґ' => 'g',
		// Czech
		'Č' => 'C',
		'Ď' => 'D',
		'Ě' => 'E',
		'Ň' => 'N',
		'Ř' => 'R',
		'Š' => 'S',
		'Ť' => 'T',
		'Ů' => 'U',
		'Ž' => 'Z',
		'č' => 'c',
		'ď' => 'd',
		'ě' => 'e',
		'ň' => 'n',
		'ř' => 'r',
		'š' => 's',
		'ť' => 't',
		'ů' => 'u',
		'ž' => 'z',
		// Polish
		'Ą' => 'A',
		'Ć' => 'C',
		'Ę' => 'e',
		'Ł' => 'L',
		'Ń' => 'N',
		'Ó' => 'o',
		'Ś' => 'S',
		'Ź' => 'Z',
		'Ż' => 'Z',
		'ą' => 'a',
		'ć' => 'c',
		'ę' => 'e',
		'ł' => 'l',
		'ń' => 'n',
		'ó' => 'o',
		'ś' => 's',
		'ź' => 'z',
		'ż' => 'z',
		// Latvian
		'Ā' => 'A',
		'Č' => 'C',
		'Ē' => 'E',
		'Ģ' => 'G',
		'Ī' => 'i',
		'Ķ' => 'k',
		'Ļ' => 'L',
		'Ņ' => 'N',
		'Š' => 'S',
		'Ū' => 'u',
		'Ž' => 'Z',
		'ā' => 'a',
		'č' => 'c',
		'ē' => 'e',
		'ģ' => 'g',
		'ī' => 'i',
		'ķ' => 'k',
		'ļ' => 'l',
		'ņ' => 'n',
		'š' => 's',
		'ū' => 'u',
		'ž' => 'z'
	);

	// Make custom replacements
	$str = preg_replace( array_keys( $options['replacements'] ), $options['replacements'], $str );

	// Transliterate characters to ASCII
	if ( $options['transliterate'] ) {
		$str = str_replace( array_keys( $char_map ), $char_map, $str );
	}

	// Replace non-alphanumeric characters with our delimiter
	$str = preg_replace( '/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str );

	// Remove duplicate delimiters
	$str = preg_replace( '/(' . preg_quote( $options['delimiter'], '/' ) . '){2,}/', '$1', $str );

	// Truncate slug to max. characters
	$str = mb_substr( $str, 0, ( $options['limit'] ? $options['limit'] : mb_strlen( $str, 'UTF-8' ) ), 'UTF-8' );

	// Remove delimiter from ends
	$str = trim( $str, $options['delimiter'] );

	return $options['lowercase'] ? mb_strtolower( $str, 'UTF-8' ) : $str;
}

//error handeling
function eazymatch_trow_error( $msg = 'EAZYMATCH ERROR' ) {
	//session_start();
	@session_destroy();
	echo "<div class=\"emol-error\">" . $msg . "</div>";
}

function emol_parse_tree( $treeElements, $checkedElements = array(), $disabledElements = array(), $name = 'competence_id', $level = - 1 ) {
	$level ++;

	if ( empty( $treeElements ) ) {
		return '';
	}
	$return = '<ul class="emol-tree emol-tree-' . $level . '">';
	foreach ( $treeElements as $treeElement ) {

		$disabled = in_array( $treeElement['id'], $disabledElements ) ? ' disabled="disabled"' : '';
		$checked  = ! $disabled & in_array( $treeElement['id'], $checkedElements ) ? ' checked="checked"' : '';

		$return .= '<li><input type="checkbox" id="' . $name . '-check-' . $treeElement['id'] . '" name="' . $name . '" value="' . $treeElement['id'] . '"' . $checked . $disabled . '><label for="' . $name . '-check-' . $treeElement['id'] . '">' . $treeElement['name'] . '</label>';

		if ( is_array( $treeElement['children'] ) && ! empty( $treeElement['children'] ) ) {
			$return .= emol_parse_tree( $treeElement['children'], $checkedElements, $disabledElements, $name, $level );
		}

		$return .= '</li>';
	}
	$return .= "</ul>";

	return $return;
}

function emol_tree_to_list( $a, $prefix = ' ', $level = 0, $parent_id = 0, $tree_root = 0, $root_index = 0 ) {
	$result      = array();
	$levelPrefix = '';
	$nextLevel   = $level + 1;

	// calculate the level prefix
	for ( $x = 0; $x < $level; $x ++ ) {
		$levelPrefix .= $prefix;
	}

	foreach ( $a as $k => $v ) {
		if ( $parent_id == 0 ) {
			$tree_root = $v['id'];
			$root_index ++;
		}

		$result[] = array(
			'id'           => (int) $v['id'],
			'name'         => $levelPrefix . $v['name'],
			'originalName' => $v['name'],
			'level'        => (int) $level,
			'parent_id'    => (int) $parent_id,
			'tree_root_id' => (int) $tree_root,
			'root_index'   => (int) $root_index
		);

		if ( isset( $v['children'] ) ) {
			foreach ( emol_tree_to_list( $v['children'], $prefix, $nextLevel, $v['id'], $tree_root, $root_index ) as $child ) {
				$result[] = $child;
			}
		}
	}

	return $result;
}

function emol_301( $newUrl ) {
	Header( "HTTP/1.1 301 Moved Permanently" );
	Header( "Location: " . $newUrl );
	exit();
}

//returns nice html for a jobrecord
function emol_parse_html_jobresult( $job, $class = '' ) {

	global $trailingData;

	$picVisible     = get_option( 'emol_job_search_logo' );
	$descVisible    = get_option( 'emol_job_search_desc' );
	$regioVisible   = get_option( 'emol_job_search_region' );
	$cityVisible    = get_option( 'emol_job_search_city' );
	$extraDate      = get_option( 'emol_job_search_date' );
	$extrastartDate = get_option( 'emol_job_search_startdate' );
	$extraendDate   = get_option( 'emol_job_search_enddate' );
	$extraHours     = get_option( 'emol_job_search_hours' );
	$competences    = get_option( 'emol_job_search_competence' );

	$competence_section = '';
	if ( ! empty( $competences ) ) {
		$competence_section .= '<div class="emol-job-result-competence">';
		foreach ( $competences as $wanted_parent ) {
			$competence_section .= '<div class="emol-job-result-competence-row"><div class="emol-job-result-competence-header">' . $wanted_parent['label'] . '</div>';
			if ( ! empty( $job['CompetenceTree'] ) ) {
				foreach ( $job['CompetenceTree'] as $oneCompetence ) {

					if ( $oneCompetence['id'] == $wanted_parent['competence_id'] ) {
						$competence_child_list = array();
						$addEmptyClass         = '';
						if ( ! empty( $oneCompetence['children'] ) ) {
							foreach ( $oneCompetence['children'] as $wanted_child ) {
								$competence_child_list[] = $wanted_child['name'];
							}
						} else {
							$competence_child_list = array( EMOL_JOB_COMPETENCE_EMPTY );
							$addEmptyClass         = 'emol-job-result-competence-empty';
						}
						$competence_section .= '<div class="emol-job-result-competence-items ' . $addEmptyClass . '">' . implode( ', ', $competence_child_list ) . '</div>';
					}
				}
			} else {
				$competence_section .= '<div class="emol-job-result-competence-items emol-job-result-competence-empty">' . EMOL_JOB_COMPETENCE_EMPTY . '</div>';
			}
			$competence_section .= '</div>';
		}
		$competence_section .= '</div>';
	}

	$text = '<div class="emol-job-result-item ' . $class . '">';

	$img = '';
	if ( $job['Company']['Logo']['content'] > '' && $picVisible == 1 ) {
		$img = '<div class="emol-job-result-logo"><img src="data:image/png;base64,' . $job['Company']['Logo']['content'] . '" /></div>';
	} elseif ( $picVisible == 1 ) {
		$img = '<div class="emol-job-result-logo"><img src="' . get_bloginfo( 'wpurl' ) . '/wp-content/plugins/eazymatch/assets/img/blank-icon.png" alt="" /></div>';
	}

	$page = get_option( 'emol_job_page' );
	if ( $page && (string) $page != '' ) {
		$job_url = get_bloginfo( 'wpurl' ) . '/' . $page . '/' . eazymatch_friendly_seo_string( $job['name'] ) . '-' . $job['id'] . $trailingData;
	} else {
		$job_url = get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_job_url' ) . '/' . $job['id'] . '/' . eazymatch_friendly_seo_string( $job['name'] ) . $trailingData;
	}

	$apply_url = get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/' . $job['id'] . '/' . eazymatch_friendly_seo_string( $job['name'] ) . $trailingData;

	$text .= $img;
	$text .= '<div class="eazymatch_job_title"><a href="' . $job_url . '">' . $job['name'] . '</a> </div>';
	$text .= $competence_section;
	if ( $descVisible == 1 ) {
		$text .= '<div class="eazymatch_job_body"> ' . emol_firstWords( clear_newline( strip_tags( nl2br( $job['description'] ) ) ) ) . '... </div>';
	}
	if ( $regioVisible == 1 && isset( $job['Address']['Region'] ) ) {
		$text .= '<div class="eazymatch_job_region">' . $job['Address']['Region']['name'] . ' </div>';
	}
	if ( $cityVisible == 1 && isset( $job['Address']['city'] ) ) {
		$text .= '<div class="eazymatch_job_city">' . $job['Address']['city'] . ' </div>';
	}

	if ( $extraDate == 1 ) {
		$jobDate = mysql2date( get_option( 'date_format' ), $job['startpublished'] );
		$text    .= '<div class="eazymatch_job_date"><span class="emol-search-label emol-jobs-search-label-date">' . EMOL_JOBSEARCH_DATE . '</span> <span class="emol-job-search-value emol-job-search-value-date">' . $jobDate . '</span></div>';
	}

	if ( $extrastartDate == 1 ) {
		if ( ! empty( $job['startpublished'] ) ) {
			$jobDate = mysql2date( get_option( 'date_format' ), $job['startdate'] );
			$text    .= '<div class="eazymatch_job_startdate"><span class="emol-search-label emol-job-search-label-startdate">' . EMOL_ADMIN_JOBSEARCH_STARTDATE . '</span> <span class="emol-job-search-value emol-job-search-value-enddate">' . $jobDate . '</span></div>';
		}
	}
	if ( $extraendDate == 1 ) {
		if ( ! empty( $job['endpublished'] ) ) {
			$jobDate = mysql2date( get_option( 'date_format' ), $job['enddate'] );
			$text    .= '<div class="eazymatch_job_enddate"><span class="emol-search-label emol-job-search-label-enddate">' . EMOL_ADMIN_JOBSEARCH_ENDDATE . '</span> <span class="emol-job-search-value emol-job-search-value-startdate">' . $jobDate . '</span></div>';
		}
	}

	if ( $extraHours == 1 ) {
		$text .= '<div class="eazymatch_job_hours"><span class="emol-job-search-value emol-job-search-value-hours">' . $job['hours'] . '</span> <span class="emol-search-label emol-job-search-label-hours">' . EMOL_JOBSEARCH_HOURS . '</span></div>';
	}

	$text .= '<div class="eazymatch_job_toolbar"><a href="' . $apply_url . '">' . EMOL_JOBSEARCH_APPLY . '</a> &nbsp; <a href="' . $job_url . '">' . EMOL_SEARCH_READMORE . '</a></div>';
	$text .= '<div class="emol-result-seperator"></div>';
	$text .= '</div>';

	return $text;

}

function emol_dump( $mixed ) {
	echo "<pre>";
	print_r( $mixed );
	echo "</pre>";
}

function emol_get_id_from_urlpart( $arg ) {
	$return = explode( '-', $arg );
	$return = array_pop( $return );
	if ( is_numeric( $return ) ) {
		return $return;
	}

	return 0;
}


function getTime() {
	$a = explode( ' ', microtime() );

	return (double) $a[0] + $a[1];
}

function emol_get_job_url( $jobdata ) {
	global $trailingData;
	$page = get_option( 'emol_job_page' );
	if ( $page && (string) $page != '' ) {
		$job_url = get_bloginfo( 'wpurl' ) . '/' . $page . '/' . eazymatch_friendly_seo_string( $jobdata['name'] ) . '-' . $jobdata['id'] . $trailingData;
	} else {
		$job_url = get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_job_url' ) . '/' . $jobdata['id'] . '/' . eazymatch_friendly_seo_string( $jobdata['name'] ) . $trailingData;
	}

	return $job_url;
}

function emol_custom_title( $title ) {

	global $emol_job;

	$emol_job_id = ( get_query_var( 'emol_job_id' ) );

	if ( isset( $emol_job_id ) && strlen( $emol_job_id ) > 2 ) {

		$emol_job_id = array_pop( explode( '-', get_query_var( 'emol_job_id' ) ) );

		if ( ! is_numeric( $emol_job_id ) ) {
			emol_301( get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_job_search_url' ) . '/all/' );
		} else {
			$emol_api = eazymatch_connect();

			$trunk = new EazyTrunk();

			// create a response array and add all the requests to the trunk
			$emol_job['job']            = &$trunk->request( 'job', 'getFullPublished', array( $emol_job_id ) );
			$emol_job['jobTexts']       = &$trunk->request( 'job', 'getCustomTexts', array( $emol_job_id ) );
			$emol_job['jobCompetences'] = &$trunk->request( 'job', 'getCompetenceTree', array( $emol_job_id ) );

			// execute the trunk request
			$trunk->execute();

			if ( empty( $emol_job['job']['name'] ) ) {
				emol_301( get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_job_search_url' ) . '/all/' );
			}

			$title = get_option( 'emol_job_header' ) . ' - ' . $emol_job['job']['name'] . ' ';
		}
	}

	return $title;
}

function emol_post( $keyName, $default = '' ) {
	global $emol_post_obj;

	return $emol_post_obj->get( $keyName, $default );
}

function emol_post_exists( $keyName ) {
	global $emol_post_obj;

	return $emol_post_obj->exists( $keyName );
}

function emol_post_set( $keyName, $value ) {
	global $emol_post_obj;

	return $emol_post_obj->set( $keyName, $value );
}

//calculate age of person
function emol_getAge( $birthday ) {
	list( $year, $month, $day ) = explode( "-", $birthday );
	$year_diff  = date( "Y" ) - $year;
	$month_diff = date( "m" ) - $month;
	$day_diff   = date( "d" ) - $day;
	if ( $month_diff < 0 ) {
		$year_diff --;
	} elseif ( ( $month_diff == 0 ) && ( $day_diff < 0 ) ) {
		$year_diff --;
	}

	return $year_diff;
}


function emol_firstWords( $string ) {
	$return = '';
	$wo     = explode( " ", $string );
	$c      = 0;
	foreach ( $wo as $piece ) {
		$c ++;
		if ( $c == ( count( $wo ) - 1 ) ) {
			break;
		}
		$return .= " " . $piece;
	}

	return trim( $return );
}

/**
 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
 * this happens (documented behavior):
 *
 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('org value', 'new value'));
 *
 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 *
 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('new value'));
 *
 * Parameters are passed by reference, though only for performance reasons. They're not
 * altered by this function.
 *
 * @param array $array1
 * @param array $array2
 *
 * @return array
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 */
if ( ! function_exists( 'array_merge_recursive_distinct' ) ) {
	function array_merge_recursive_distinct( array &$array1, array &$array2 ) {
		$merged = $array1;

		foreach ( $array2 as $key => &$value ) {
			if ( is_array( $value ) && isset ( $merged [ $key ] ) && is_array( $merged [ $key ] ) ) {
				$merged [ $key ] = array_merge_recursive_distinct( $merged [ $key ], $value );
			} else {
				$merged [ $key ] = $value;
			}
		}

		return $merged;
	}
}
/*** DEBUGGING ***/
function eazymatch_start_debug() {
	ob_clean();
	echo '<B>' . date( "H:i:s" ) . '</B><br>';
	echo "<div class=\"emol-error\">";
	echo "<pre>";
}

function eazymatch_end_debug() {
	echo "</pre></div>";
	echo '<B>' . date( "H:i:s" ) . '</B>';
	exit();
}

function emol_debug( $var ) {
	echo "<pre>";
	print_R( $var );
	echo "</pre>";
	exit();
}

if ( ! function_exists( 'clear_newline' ) ) {
	/**
	 * Replace all linebreaks with one whitespace.
	 *
	 * @access public
	 *
	 * @param string $string
	 *   The text to be processed.
	 *
	 * @return string
	 *   The given text without any linebreaks.
	 */
	function clear_newline( $string ) {
		return (string) str_replace( array( "\r", "\r", "" ), '', $string );
	}
}

/* create support for class_alias before php 5.3 */
if ( ! function_exists( 'class_alias' ) ) {
	function class_alias( $original, $alias ) {
		eval( 'class ' . $alias . ' extends ' . $original . ' {}' );
	}
}

/**
 * @param $prefix
 */
function emol_delete_options_prefixed( $prefix ) {
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'" );
}

/**
 * returns the current eazymatch connect Instance
 *
 * @return emol_connectsvn mv co
 */
function eazymatch_connect() {
	$connectionManager = emol_connectManager::getInstance();

	return $connectionManager->getConnection();
}


function emol_view_show( $viewName, $data = array() ) {
	$viewManager = emol_view::getInstance();
	$viewManager->show( $viewName, $data );
}

function emol_view_load( $viewName, $data = array() ) {
	$viewManager = emol_view::getInstance();

	return $viewManager->load( $viewName, $data );
}

/**
 * creates 3 comboboxes for selecting a date
 *
 * @param mixed $inName
 * @param int $useDate
 * @param mixed $extraCls
 *
 * @return mixed
 */

function emol_dateselector( $inName, $useDate = 0, $extraCls = '' ) {
	/* create array so we can name months */
	$monthName = ARRAY(
		1 => "01",
		"02",
		"03",
		"04",
		"05",
		"06",
		"07",
		"08",
		"09",
		"10",
		"11",
		"12"
	);

	/* if date invalid or not supplied, use current time */
	$skipSelected = false;
	if ( $useDate == 0 ) {
		$useDate      = time();
		$skipSelected = true;
	} else {
		$useDate = strtotime( $useDate );
	}

	/* make month selector */
	$returnMonth = "<SELECT NAME=\"" . $inName . "-month\" class=\"emol-date-month $extraCls\"><option></option>";
	for ( $currentMonth = 1; $currentMonth <= 12; $currentMonth ++ ) {
		$returnMonth .= "<OPTION VALUE=\"";
		$returnMonth .= INTVAL( $currentMonth );
		$returnMonth .= "\"";
		if ( $skipSelected === false ) {
			IF ( INTVAL( DATE( "m", $useDate ) ) == $currentMonth ) {
				$returnMonth .= " SELECTED";
			}
		}
		$returnMonth .= ">" . $monthName[ $currentMonth ] . "";
	}
	$returnMonth .= "</SELECT>";

	/* make day selector */
	$returnDay = "<SELECT NAME=\"" . $inName . "-day\"  class=\"emol-date-day $extraCls\"><option></option>";
	for ( $currentDay = 1; $currentDay <= 31; $currentDay ++ ) {
		$returnDay .= "<OPTION VALUE=\"$currentDay\"";
		if ( $skipSelected === false ) {
			IF ( INTVAL( DATE( "d", $useDate ) ) == $currentDay ) {
				$returnDay .= " SELECTED";
			}
		}
		$returnDay .= ">$currentDay";
	}
	$returnDay .= "</SELECT>";

	/* make year selector */
	$returnYear = "<SELECT NAME=\"" . $inName . "-year\" class=\"emol-date-year $extraCls\"><option></option>";
	$startYear  = DATE( 'Y' );
	for ( $currentYear = $startYear; $currentYear > $startYear - 90; $currentYear -- ) {
		$returnYear .= "<OPTION VALUE=\"$currentYear\"";
		if ( $skipSelected === false ) {
			if ( DATE( "Y", $useDate ) == $currentYear ) {
				$returnYear .= " SELECTED";
			}
		}
		$returnYear .= ">$currentYear";
	}
	$returnYear .= "</SELECT>";

	return array( 'day' => $returnDay, 'month' => $returnMonth, 'year' => $returnYear );

}

// A copy of rel_canonical but to allow an override on a custom tag
function rel_canonical_with_custom_tag_override() {
	if ( ! is_singular() ) {
		return;
	}

	global $wp_the_query;
	if ( ! $id = $wp_the_query->get_queried_object_id() ) {
		return;
	}

	// check whether the current post has content in the "canonical_url" custom field
	$canonical_url = get_post_meta( $id, 'canonical_url', true );
	if ( '' != $canonical_url ) {
		// trailing slash functions copied from http://core.trac.wordpress.org/attachment/ticket/18660/canonical.6.patch
		$link = user_trailingslashit( trailingslashit( $canonical_url ) );
	} else {
		$link = get_permalink( $id );
	}
	$link = get_site_url() . $_SERVER['REQUEST_HOST'] . $_SERVER['REQUEST_URI'];
	echo "<link rel='canonical' href='" . esc_url( $link ) . "' />\n";
}
