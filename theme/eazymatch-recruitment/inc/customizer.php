<?php
/**
 * Customizer settings and the CSS they generate.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default values for every theme option.
 *
 * @return array<string, string>
 */
function emr_defaults() {
	return array(
		'emr_brand_color'      => '#1f5eff',
		'emr_accent_color'     => '#00c281',
		'emr_hero_title'       => __( 'Vind de baan die bij je past', 'eazymatch-recruitment' ),
		'emr_hero_text'        => __( 'Bekijk onze actuele vacatures, filter op regio en vakgebied en solliciteer in een paar minuten.', 'eazymatch-recruitment' ),
		'emr_hero_eyebrow'     => __( 'Werken bij ons', 'eazymatch-recruitment' ),
		'emr_hero_image'       => '',
		'emr_hero_button_text' => __( 'Bekijk alle vacatures', 'eazymatch-recruitment' ),
		'emr_hero_button_url'  => '',
		'emr_hero_show_search' => '1',
		'emr_hero_stats'       => '',
		'emr_jobs_title'       => __( 'Actuele vacatures', 'eazymatch-recruitment' ),
		'emr_jobs_text'        => __( 'Een selectie uit ons actuele aanbod. Staat jouw baan er niet bij? Laat je gegevens achter voor een open sollicitatie.', 'eazymatch-recruitment' ),
		'emr_jobs_limit'       => '6',
		'emr_jobs_competences' => '',
		'emr_jobs_teaser_text' => '',
		'emr_cta_title'        => __( 'Geen passende vacature gevonden?', 'eazymatch-recruitment' ),
		'emr_cta_text'         => __( 'Stuur een open sollicitatie. We nemen contact op zodra er een passende functie vrijkomt.', 'eazymatch-recruitment' ),
		'emr_cta_button_text'  => __( 'Open sollicitatie', 'eazymatch-recruitment' ),
		'emr_cta_button_url'   => '',
		'emr_header_cta_text'  => __( 'Vacatures', 'eazymatch-recruitment' ),
		'emr_header_cta_url'   => '',
		'emr_footer_text'      => '',
	);
}

/**
 * Read a theme option with its default.
 *
 * @param string $key Option key.
 *
 * @return string
 */
function emr_option( $key ) {
	$defaults = emr_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return (string) get_theme_mod( $key, $default );
}

/**
 * Register customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function emr_customize_register( $wp_customize ) {
	$defaults = emr_defaults();

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	/* ---------------------------------------------------------------- */
	/* Colours                                                           */
	/* ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'emr_colors',
		array(
			'title'    => __( 'Kleuren', 'eazymatch-recruitment' ),
			'priority' => 25,
		)
	);

	$colors = array(
		'emr_brand_color'  => __( 'Hoofdkleur', 'eazymatch-recruitment' ),
		'emr_accent_color' => __( 'Accentkleur', 'eazymatch-recruitment' ),
	);

	foreach ( $colors as $key => $label ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$key,
				array(
					'label'   => $label,
					'section' => 'emr_colors',
				)
			)
		);
	}

	/* ---------------------------------------------------------------- */
	/* Header                                                            */
	/* ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'emr_header',
		array(
			'title'       => __( 'Header', 'eazymatch-recruitment' ),
			'priority'    => 26,
			'description' => __( 'De knop rechts in de header. Laat de url leeg om automatisch naar de vacaturepagina uit de EazyMatch-instellingen te linken.', 'eazymatch-recruitment' ),
		)
	);

	emr_add_text_setting( $wp_customize, 'emr_header_cta_text', __( 'Tekst op de knop', 'eazymatch-recruitment' ), 'emr_header' );
	emr_add_text_setting( $wp_customize, 'emr_header_cta_url', __( 'Url van de knop', 'eazymatch-recruitment' ), 'emr_header', 'url' );

	/* ---------------------------------------------------------------- */
	/* Hero                                                              */
	/* ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'emr_hero',
		array(
			'title'       => __( 'Homepage: hero', 'eazymatch-recruitment' ),
			'priority'    => 27,
			'description' => __( 'Zichtbaar op de voorpagina.', 'eazymatch-recruitment' ),
		)
	);

	emr_add_text_setting( $wp_customize, 'emr_hero_eyebrow', __( 'Bovenkopje', 'eazymatch-recruitment' ), 'emr_hero' );
	emr_add_text_setting( $wp_customize, 'emr_hero_title', __( 'Titel', 'eazymatch-recruitment' ), 'emr_hero' );
	emr_add_text_setting( $wp_customize, 'emr_hero_text', __( 'Introtekst', 'eazymatch-recruitment' ), 'emr_hero', 'textarea' );
	emr_add_text_setting( $wp_customize, 'emr_hero_button_text', __( 'Tekst op de knop', 'eazymatch-recruitment' ), 'emr_hero' );
	emr_add_text_setting( $wp_customize, 'emr_hero_button_url', __( 'Url van de knop', 'eazymatch-recruitment' ), 'emr_hero', 'url' );

	$wp_customize->add_setting(
		'emr_hero_image',
		array(
			'default'           => $defaults['emr_hero_image'],
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'emr_hero_image',
			array(
				'label'       => __( 'Achtergrondafbeelding', 'eazymatch-recruitment' ),
				'description' => __( 'Optioneel. Zonder afbeelding wordt een kleurverloop in de hoofdkleur getoond.', 'eazymatch-recruitment' ),
				'section'     => 'emr_hero',
			)
		)
	);

	$wp_customize->add_setting(
		'emr_hero_show_search',
		array(
			'default'           => $defaults['emr_hero_show_search'],
			'sanitize_callback' => 'emr_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'emr_hero_show_search',
		array(
			'label'       => __( 'Zoekformulier in de hero tonen', 'eazymatch-recruitment' ),
			'description' => __( 'Plaatst [eazymatch view="searchjobs"] onder de introtekst.', 'eazymatch-recruitment' ),
			'section'     => 'emr_hero',
			'type'        => 'checkbox',
		)
	);

	emr_add_text_setting(
		$wp_customize,
		'emr_hero_stats',
		__( 'Kerncijfers', 'eazymatch-recruitment' ),
		'emr_hero',
		'textarea',
		__( 'Eén per regel, in de vorm "waarde | omschrijving". Bijvoorbeeld: 120+ | openstaande vacatures', 'eazymatch-recruitment' )
	);

	/* ---------------------------------------------------------------- */
	/* Job list on the homepage                                          */
	/* ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'emr_jobs',
		array(
			'title'       => __( 'Homepage: vacatures', 'eazymatch-recruitment' ),
			'priority'    => 28,
			'description' => __( 'Stuurt de shortcode [eazymatch view="jobs"] aan.', 'eazymatch-recruitment' ),
		)
	);

	emr_add_text_setting( $wp_customize, 'emr_jobs_title', __( 'Titel', 'eazymatch-recruitment' ), 'emr_jobs' );
	emr_add_text_setting( $wp_customize, 'emr_jobs_text', __( 'Introtekst', 'eazymatch-recruitment' ), 'emr_jobs', 'textarea' );
	emr_add_text_setting(
		$wp_customize,
		'emr_jobs_limit',
		__( 'Aantal vacatures', 'eazymatch-recruitment' ),
		'emr_jobs',
		'number'
	);
	emr_add_text_setting(
		$wp_customize,
		'emr_jobs_competences',
		__( 'Competentie-id\'s', 'eazymatch-recruitment' ),
		'emr_jobs',
		'text',
		__( 'Kommagescheiden id\'s uit het EazyMatch-matchprofiel, bijvoorbeeld 31,152. Leeg laten om niet te filteren.', 'eazymatch-recruitment' )
	);
	emr_add_text_setting(
		$wp_customize,
		'emr_jobs_teaser_text',
		__( 'Tekstblok als korte tekst', 'eazymatch-recruitment' ),
		'emr_jobs',
		'text',
		__( 'Id of titel van een tekstblok van de vacature (job-teaser-text). De beschikbare id\'s staan onder EazyMatch > Shortcodes.', 'eazymatch-recruitment' )
	);

	/* ---------------------------------------------------------------- */
	/* Call to action                                                    */
	/* ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'emr_cta',
		array(
			'title'    => __( 'Homepage: oproep', 'eazymatch-recruitment' ),
			'priority' => 29,
		)
	);

	emr_add_text_setting( $wp_customize, 'emr_cta_title', __( 'Titel', 'eazymatch-recruitment' ), 'emr_cta' );
	emr_add_text_setting( $wp_customize, 'emr_cta_text', __( 'Tekst', 'eazymatch-recruitment' ), 'emr_cta', 'textarea' );
	emr_add_text_setting( $wp_customize, 'emr_cta_button_text', __( 'Tekst op de knop', 'eazymatch-recruitment' ), 'emr_cta' );
	emr_add_text_setting( $wp_customize, 'emr_cta_button_url', __( 'Url van de knop', 'eazymatch-recruitment' ), 'emr_cta', 'url' );

	/* ---------------------------------------------------------------- */
	/* Footer                                                            */
	/* ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'emr_footer',
		array(
			'title'    => __( 'Footer', 'eazymatch-recruitment' ),
			'priority' => 30,
		)
	);

	emr_add_text_setting( $wp_customize, 'emr_footer_text', __( 'Tekst onderin', 'eazymatch-recruitment' ), 'emr_footer', 'textarea' );
}
add_action( 'customize_register', 'emr_customize_register' );

/**
 * Helper that registers a simple text-like setting plus control.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 * @param string               $key          Setting key.
 * @param string               $label        Control label.
 * @param string               $section      Section id.
 * @param string               $type         Control type: text, textarea, url or number.
 * @param string               $description  Optional description.
 */
function emr_add_text_setting( $wp_customize, $key, $label, $section, $type = 'text', $description = '' ) {
	$defaults = emr_defaults();

	switch ( $type ) {
		case 'url':
			$sanitize = 'esc_url_raw';
			break;
		case 'number':
			$sanitize = 'absint';
			break;
		case 'textarea':
			$sanitize = 'wp_kses_post';
			break;
		default:
			$sanitize = 'sanitize_text_field';
	}

	$wp_customize->add_setting(
		$key,
		array(
			'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
			'sanitize_callback' => $sanitize,
		)
	);

	$wp_customize->add_control(
		$key,
		array(
			'label'       => $label,
			'description' => $description,
			'section'     => $section,
			'type'        => 'number' === $type ? 'number' : ( 'textarea' === $type ? 'textarea' : ( 'url' === $type ? 'url' : 'text' ) ),
		)
	);
}

/**
 * Checkbox sanitizer.
 *
 * @param mixed $value Raw value.
 *
 * @return string
 */
function emr_sanitize_checkbox( $value ) {
	return ( ! empty( $value ) ) ? '1' : '';
}

/**
 * Darken a hex colour by a percentage.
 *
 * @param string $hex     Hex colour.
 * @param int    $percent Percentage to darken, 0-100.
 *
 * @return string
 */
function emr_darken_color( $hex, $percent ) {
	$hex = ltrim( (string) $hex, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '#' . $hex;
	}

	$factor = max( 0, 1 - ( $percent / 100 ) );
	$parts  = array();

	foreach ( str_split( $hex, 2 ) as $channel ) {
		$parts[] = str_pad( dechex( (int) round( hexdec( $channel ) * $factor ) ), 2, '0', STR_PAD_LEFT );
	}

	return '#' . implode( '', $parts );
}

/**
 * Convert a hex colour to "r, g, b".
 *
 * @param string $hex Hex colour.
 *
 * @return string
 */
function emr_hex_to_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '31, 94, 255';
	}

	$parts = array_map( 'hexdec', str_split( $hex, 2 ) );

	return implode( ', ', $parts );
}

/**
 * Inline CSS built from the customizer colours.
 *
 * @return string
 */
function emr_customizer_css() {
	$brand  = emr_option( 'emr_brand_color' );
	$accent = emr_option( 'emr_accent_color' );
	$rgb    = emr_hex_to_rgb( $brand );

	return sprintf(
		':root{--emr-brand:%1$s;--emr-brand-dark:%2$s;--emr-brand-soft:rgba(%3$s,.10);--emr-accent:%4$s;}',
		$brand,
		emr_darken_color( $brand, 18 ),
		$rgb,
		$accent
	);
}
