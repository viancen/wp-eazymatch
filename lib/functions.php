<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

function get_emol_dummy_post_object() {
	/**
	 * What we are going to do here, is create a fake post.  A post
	 * that doesn't actually exist. We're gonna fill it up with
	 * whatever values you want.  The content of the post will be
	 * the output from your plugin.
	 */


	/**
	 * Create a fake post.
	 */
	$post = new stdClass();
	/**
	 * Page type is post page huh
	 */
	$post->post_type   = 'page';
	$post->post_parent = '';
	/**
	 * Static means a page, not a post.
	 */
	$post->post_status = 'static';

	/**
	 * Turning off comments for the post.
	 */
	$post->comment_status = 'closed';

	/**
	 * The filter="raw" step is important. Other examples I found for creating fake post objects didn’t include this step. I encountered numerous problems without this. The reason being the core get_post() function. This function is used everywhere in WordPress. If you look at this you’ll notice in the following code:
	 *
	 */
	$post->filter = 'raw'; // important!

	/**
	 * The author ID for the post.  Usually 1 is the sys admin.  Your
	 * plugin can find out the real author ID without any trouble.
	 */
	$post->post_author  = 1;
	$post->post_title   = '-';
	$post->post_content = '';
	$post->post_name    = '-';

	$rand       = rand( 1, 999999 );
	$post->guid = 'emol-rand-' . $rand;


	/**
	 * Fake post ID to prevent WP from trying to show comments for
	 * a post that doesn't really exist.
	 */
	$post->ID = null;

	/**
	 * Let people ping the post?  Probably doesn't matter since
	 * comments are turned off, so not sure if WP would even
	 * show the pings.
	 */
	$post->ping_status   = 'open';
	$post->comment_count = 0;
	$post->public        = true;

	/**
	 * You can pretty much fill these up with anything you want.  The
	 * current date is fine.  It's a fake post right?  Maybe the date
	 * the plugin was activated?
	 */
	$post->post_date     = current_time( 'mysql' );
	$post->post_date_gmt = current_time( 'mysql', 1 );

	return $post;
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

	if ( ! empty( $job['Statusses'] ) ) {
		foreach ( $job['Statusses'] as $aroStat ) {
			$class .= ' emol-job-status-' . $aroStat['jobstatus_id'];
		}
	}
	$text = '<div class="emol-job-result-item ' . $class . '">';

	$img = '';
	if ( $job['Company']['Logo']['content'] > '' && $picVisible == 1 ) {
		$img = '<div class="emol-job-result-logo"><img src="data:image/png;base64,' . $job['Company']['Logo']['content'] . '" /></div>';
	} elseif ( $picVisible == 1 ) {
		$img = '<div class="emol-job-result-logo"><img src="' . get_bloginfo( 'wpurl' ) . '/wp-content/plugins/wp-eazymatch/assets/img/blank-icon.png" alt="" /></div>';
	}

	$page = get_option( 'emol_job_page' );
	if ( $page && (string) $page != '' ) {
		$job_url = get_bloginfo( 'wpurl' ) . '/' . $page . '/' . eazymatch_friendly_seo_string( $job['name'] ) . '-' . $job['id'] . $trailingData;
	} else {
		$job_url = get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_job_url' ) . '/' . $job['id'] . '/' . eazymatch_friendly_seo_string( $job['name'] ) . $trailingData;
	}

	$pagex = get_option( 'emol_apply_page' );
	if ( $pagex && (string) $pagex != '' ) {
		$apply_url = get_bloginfo( 'wpurl' ) . '/' . $pagex . '/' . eazymatch_friendly_seo_string( $job['name'] ) . '-' . $job['id'] . $trailingData;
	} else {
		$pagex = get_option( 'emol_apply_url' );
		$apply_url = get_bloginfo( 'wpurl' ) . '/' . $pagex . '/' . $job['id'] . '/' . eazymatch_friendly_seo_string( $job['name'] ) . $trailingData;
	}

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

function emol_get_apply_form( $jobData ) {

	$api = eazymatch_connect();

	$data = array();

	//fillup default array of presets
	$data['birthdate']  = '';
	$data['firstname']  = '';
	$data['lastname']   = '';
	$data['middlename'] = '';

	$data['city']             = '';
	$data['ssn']              = '';
	$data['nationality_id']   = '';
	$data['street']           = '';
	$data['country_id']       = '';
	$data['maritalstatus_id'] = '';

	$data['availablehours']    = '';
	$data['description']       = '';
	$data['title']             = '';
	$data['housenumber']       = '';
	$data['phonenumber']       = '';
	$data['phonenumber2']      = '';
	$data['managercompany_id'] = '';
	$data['schoolingtype_id']  = '';
	$data['zipcode']           = '';
	$data['email']             = '';
	$data['extension']         = '';
	$data['competence']        = array();

	//fill up default data
	if ( isset( $defaultData ) && count( $defaultData ) > 0 && isset( $defaultData['birthdate-year'] ) && isset( $defaultData['birthdate-month'] ) && isset( $defaultData['birthdate-day'] ) ) {
		$data              = $defaultData;
		$data['birthdate'] = $defaultData['birthdate-year'] . '-' . $defaultData['birthdate-month'] . '-' . $defaultData['birthdate-day'];
	}

	// prepare client resources
	// emol_require::validation();
	// emol_require::jqueryUi();

	if ( isset( $data['linkedInrequest'] ) ) {
		$linkedInrequest = $data['linkedInrequest'];
	} else {
		$linkedInrequest = ( get_query_var( 'emolrequestid' ) );
	}


	$inImage = 'connect-to-linkedin.png';
	if ( strlen( $linkedInrequest ) == 128 ) {

		$appApi = $api->get( 'applicant' );
		$dataIn = $appApi->getLinkedInProfile( $linkedInrequest );


		if ( ! empty( $dataIn['date-of-birth'] ) ) {
			$data['birthdate'] = $dataIn['date-of-birth']['year'] . '-' . $dataIn['date-of-birth']['month'] . '-' . $dataIn['date-of-birth']['day'];
		}

		//normalize data
		$data['title']       = @$dataIn['headline'];
		$data['email']       = @$dataIn['email-address'];
		$data['phonenumber'] = @$dataIn['phone-numbers']['phone-number']['phone-number'];
		$data['description'] = @$dataIn['summary'];
		$data['city']        = @$dataIn['location']['name'];
		$dataIn['last-name'] = explode( ' ', $dataIn['last-name'] );
		$data['lastname']    = array_pop( $dataIn['last-name'] );
		$data['firstname']   = $dataIn['first-name'];
		$data['middlename']  = implode( ' ', $dataIn['last-name'] );
		//$data['birthdate'] = $dataIn['birthdate-year'].'-'.$dataIn['birthdate-month'].'-'.$dataIn['birthdate-day'];
		$inImage = 'connected-to-linkedin.png';
	}

	$firstDescription = '';
	if ( isset( $jobData['description'] ) && trim( $jobData['description'] != '' ) ) {
		$firstDescription = '<p id="emol-apply-job-summary">' . EMOL_APPLY_HEADER . ' <strong>' . $jobData['name'] . '</strong>.</p>';
	}

	//the apply form
	include( EMOL_DIR . '/lib/emol/loginwidget.php' );

	$mailto = get_option( 'emol_email' );

	$applyHtml = $firstDescription . $loginWidget . '
        <div id="emol-form-apply" class="emol-form-div emol-form-table">
        <form method="post" id="emol-apply-form" enctype="multipart/form-data" action="' . get_bloginfo( 'wpurl' ) . '/em-submit-subscription">
        <input type="hidden" name="job_id" value="' . $jobData['id'] . '" />
        <input type="hidden" name="EMOL_apply" value="1" />
        <input type="hidden" name="linkedInrequest" value="' . $linkedInrequest . '" />';

	$urlVars = explode( '/', $_SERVER['REQUEST_URI'] );

	//url applying
	$url = ( ! empty( $_SERVER['HTTPS'] ) ) ? "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	//$url = $url . '/';

	if ( in_array( 'success', $urlVars ) ) {
		$applyHtml .= '<tr><td colspan="2">' . stripslashes( get_option( 'emol_apply_success' ) ) . '</td></tr>';
	} elseif ( isset( $urlVars[2] ) && $urlVars[2] == 'unsuccess' ) {
		$applyHtml .= '<tr><td colspan="2">' . EMOL_APPLY_FAIL_MSG . '</td></tr>';
	} else {
		$applyHtml .= '<tr><td colspan="2" class="emol-apply-mandatory">' . EMOL_APPLY_MANDATORY . '</td></tr>';
		$applyHtml .= '<div id="emol-form-wrapper">';
		include( EMOL_DIR . '/lib/emol/applyform.php' );
		$applyHtml .= '</div>';
	}

	//finish up html
	$applyHtml .= '</div>';

	//return some html
	return str_replace( PHP_EOL, ' ', $applyHtml );
}

function emol_post_application() {

	$emolApi = eazymatch_connect();
	//captcha didnt check out....
	//initiate webservice method
	$ws     = $emolApi->get( 'applicant' );
	$wsTool = $emolApi->get( 'tool' );

	//fetch birthdate parts
	$birthdate = null;
	$yeartest  = emol_post( 'birthdate-year' );
	if ( ! empty( $yeartest ) ) {
		$birthdate = emol_post( 'birthdate-year' ) . '-' . emol_post( 'birthdate-month' ) . '-' . emol_post( 'birthdate-day' );
	}

	if ( ! emol_session::isValidId( 'applicant_id' ) ) {

		//create a array the way EazyMatch likes it
		$subscription = new emol_ApplicantMutation();

		//set the person
		$subscription->setPerson(
			null,
			emol_post( 'firstname' ),
			emol_post( 'middlename' ),
			emol_post( 'lastname' ),
			$birthdate,
			emol_post( 'email' ),
			emol_post( 'password' ),
			emol_post( 'gender' ),
			emol_post( 'ssn' ),
			emol_post( 'nationality_id' ),
			emol_post( 'managercompany_id' )
		);


		//set the Applicant
		$subscription->setApplicant(
			null,
			date( 'Ymd' ),
			date( 'Ymd' ),
			null,
			emol_post( 'title' ),
			emol_post( 'healthcarereference' ),
			emol_post( 'linkedInrequest' ),
			emol_post( 'contactvia' ),
			emol_post( 'maritalstatus_id' ),
			emol_post( 'searchlocation' ),
			emol_post( 'salary' ),
			emol_post( 'availablehours' )
		);

		//set addresses
		if ( isset( $_POST['street'] ) && $_POST['street'] != '' ) {

			$subscription->addAddress(
				null,
				null,
				emol_post( 'country_id' ),
				null,
				emol_post( 'street' ),
				emol_post( 'housenumber' ),
				emol_post( 'extension' ),
				emol_post( 'zipcode' ),
				emol_post( 'city' )
			);

		} elseif ( isset( $_POST['zipcode'] ) && $_POST['zipcode'] != '' ) {

			$addrPiece = $emolApi->getAddressByZipcode( emol_post( 'zipcode' ) );

			$addrPiece['province_id'] = ( isset( $addrPiece['province_id'] ) ? $addrPiece['province_id'] : null );
			$addrPiece['country_id']  = ( isset( $addrPiece['country_id'] ) ? $addrPiece['country_id'] : null );
			$addrPiece['region_id']   = ( isset( $addrPiece['region_id'] ) ? $addrPiece['region_id'] : null );
			$addrPiece['street']      = ( isset( $addrPiece['street'] ) ? $addrPiece['street'] : null );
			$addrPiece['zipcode']     = ( isset( $addrPiece['zipcode'] ) ? $addrPiece['zipcode'] : null );
			$addrPiece['city']        = ( isset( $addrPiece['city'] ) ? $addrPiece['city'] : null );

			$subscription->addAddress(
				null,
				$addrPiece['province_id'],
				$addrPiece['country_id'],
				$addrPiece['region_id'],
				$addrPiece['street'],
				emol_post( 'housenumber' ),
				emol_post( 'extension' ),
				$addrPiece['zipcode'],
				$addrPiece['city']
			);

		}

		/**email**/
		$subscription->addEmailaddresses( null, null, emol_post( 'email' ) );
		/**phonenumber**/
		if ( get_option( 'emol_frm_app_phone' ) !== '' ) {
			$subscription->addPhonenumber( null, null, emol_post( 'phonenumber' ) );
		}

		if ( get_option( 'emol_frm_app_phone2' ) !== '' ) {
			$subscription->addPhonenumber( null, null, emol_post( 'phonenumber2' ) );
		}


		if ( get_option( 'emol_frm_app_schoolingtype_id' ) !== '' ) {
			$emol_frm_app_schoolingtype_id = emol_post( 'schoolingtype_id' );

			if ( is_numeric( $emol_frm_app_schoolingtype_id ) ) {
				$subscription->addSchooling( $emol_frm_app_schoolingtype_id );
			}
		}

		//CV
		if ( isset( $_FILES['cv'] ) && isset( $_FILES['cv']['tmp_name'] ) && $_FILES['cv']['tmp_name'] != '' ) {
			//set the CV document
			$doc            = array();
			$doc['name']    = $_FILES['cv']['name'];
			$doc['content'] = base64_encode( file_get_contents( $_FILES['cv']['tmp_name'] ) );
			$doc['type']    = $_FILES['cv']['type'];

			$subscription->setCV( $doc['name'], $doc['type'], $doc['content'] );
		}

		//photo
		if ( isset( $_FILES['picture'] ) && isset( $_FILES['picture']['tmp_name'] ) && $_FILES['picture']['tmp_name'] != '' ) {
			//set the CV document
			$doc            = array();
			$doc['name']    = $_FILES['picture']['name'];
			$doc['content'] = base64_encode( file_get_contents( $_FILES['picture']['tmp_name'] ) );
			$doc['type']    = $_FILES['picture']['type'];


			$subscription->setPicture( $doc['name'], $doc['type'], $doc['content'] );
		}

		//competences
		$competenceElements = get_option( 'emol_frm_app_competence', array() );
		foreach ( $competenceElements as $competence ) {
			if ( emol_post_exists( 'competence' . $competence['competence_id'] ) ) {
				foreach ( emol_post( 'competence' . $competence['competence_id'] ) as $cpt ) {
					$subscription->addCompetence( $cpt );
				}
			}
		}

		//job / mediation / match
		if ( emol_post( 'job_id' ) == '' ) {
			emol_post_set( 'job_id', null );
		}

		$url = $_SERVER['HTTP_HOST'];

		$contentMessage = nl2br( emol_post( 'motivation' ) );
		$contentMessage .= '<br /><br />(' . $url . ')';

		$subscription->setApplication(
			emol_post( 'job_id' ), $contentMessage, $url
		);

		//create the workable postable array
		$postData = $subscription->createSubscription();

		//option to directly go in the sys instead of webaanmeldingen
		$postData['processDirectly'] = get_option( 'emol_apply_process_directly' );

		//save the subscription to EazyMatch, this will send an notification to emol user and an email to the subscriber
		$ws->subscription( $postData );


		//naar eigen url of naar globale
		$redirectUrl = get_option( 'emol_apply_url_success_redirect' );
		if ( ! empty( $redirectUrl ) ) {
			return ( $redirectUrl );
		} else {
			return ( get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/' . emol_post( 'job_id' ) . '/success/' );
		}
	} else {
		/**
		 * apply to job, the true in the end is for triggering mail event
		 * EazyMatch will create a mediation between the job and applicant with the motivation.
		 * It also will register a correspondence moment and will send an e-mail to the emol user ( notification )
		 **/
		$success = $ws->applyToJob( emol_post( 'job_id' ), emol_session::get( 'applicant_id' ), nl2br( emol_post( 'motivation' ) ), true );
		if ( $success == true ) {
			return ( get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/' . $this->jobId . '/success/' );
		} else {
			return ( get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/' . $this->jobId . '/unsuccess/' );
		}
	}
}


function emol_custom_title( $title ) {

	global $emol_job;

	$emol_job_id = ( get_query_var( 'emol_job_id' ) );

	if ( isset( $emol_job_id ) && strlen( $emol_job_id ) > 2 ) {

		$arref       = explode( '-', get_query_var( 'emol_job_id' ) );
		$emol_job_id = array_pop( $arref );

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

/* Get Parameters from $_POST and $_GET (WordPress)
    $param = string name of specific parameter requested (default to null, get all parameters
    $null_return = what you want returned if the parameter is not set (null, false, array() etc

    returns $params (string or array depending upon $param) of either parameter value or all parameters by key and value

    Note:   POST overrules GET (if both are set with a value and GET overrules POST if POST is not set or has a non-truthful value
            All parameters are trimmed and sql escaped
*/

function emol_wordpress_get_params( $param = null, $null_return = null ) {
	if ( $param ) {
		$value = ( ! empty( $_POST[ $param ] ) ? trim( esc_sql( $_POST[ $param ] ) ) : ( ! empty( $_GET[ $param ] ) ? trim( esc_sql( $_GET[ $param ] ) ) : $null_return ) );

		return $value;
	} else {

		$params = array();
		foreach ( $_POST as $key => $param ) {
			if ( is_array( $_POST[ $key ] ) ) {
				foreach ( $_POST[ $key ] as $sKey => $sVals ) {
					$params[ trim( esc_sql( $key ) ) ][] = ( ! empty( $_POST[ $key ][ $sKey ] ) ? trim( esc_sql( $_POST[ $key ][ $sKey ] ) ) : $null_return );
				}
			} else {
				$params[ trim( esc_sql( $key ) ) ] = ( ! empty( $_POST[ $key ] ) ? trim( esc_sql( $_POST[ $key ] ) ) : $null_return );
			}
		}
		foreach ( $_GET as $key => $param ) {
			$key = trim( esc_sql( $key ) );
			if ( ! isset( $params[ $key ] ) ) { // if there is no key or it's a null value
				$params[ trim( esc_sql( $key ) ) ] = ( ! empty( $_GET[ $key ] ) ? trim( esc_sql( $_GET[ $key ] ) ) : $null_return );
			}
		}

		return $params;
	}
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
	if ( ! isset( $_SERVER['REQUEST_HOST'] ) ) {
		$_SERVER['REQUEST_HOST'] = '';
	}
	if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
		$_SERVER['REQUEST_URI'] = '';
	}
	$link = get_site_url() . $_SERVER['REQUEST_HOST'] . $_SERVER['REQUEST_URI'];
	echo "<link rel='canonical' href='" . esc_url( $link ) . "' />\n";
}
