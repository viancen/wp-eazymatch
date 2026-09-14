<?php
/**
 * Template helpers.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build the [eazymatch view="jobs"] shortcode from the customizer settings.
 *
 * @return string
 */
function emr_jobs_shortcode() {
	$shortcode = '[eazymatch view="jobs"';

	$limit = absint( emr_option( 'emr_jobs_limit' ) );
	if ( $limit > 0 ) {
		$shortcode .= ' limit="' . $limit . '"';
	}

	$competences = emr_option( 'emr_jobs_competences' );
	$competences = implode( ',', array_filter( array_map( 'absint', explode( ',', $competences ) ) ) );
	if ( '' !== $competences ) {
		$shortcode .= ' competences="' . $competences . '"';
	}

	$teaser = trim( emr_option( 'emr_jobs_teaser_text' ) );
	if ( '' !== $teaser ) {
		$shortcode .= ' job-teaser-text="' . esc_attr( $teaser ) . '"';
	}

	return $shortcode . ']';
}

/**
 * Build the [eazymatch view="searchjobs"] shortcode.
 *
 * The settings attribute is required by the plugin.
 *
 * @param string $title  Heading above the form; may be empty.
 * @param string $button Label of the submit button.
 * @param string $reset  Label of the link that clears all filters.
 *
 * @return string
 */
function emr_searchjobs_shortcode( $title = '', $button = '', $reset = '' ) {
	if ( '' === $button ) {
		$button = __( 'Zoeken', 'eazymatch-recruitment' );
	}

	if ( '' === $reset ) {
		$reset = __( 'Alle vacatures tonen', 'eazymatch-recruitment' );
	}

	// The plugin splits on | and =, so those characters cannot appear in a value.
	$clean = static function ( $value ) {
		return trim( str_replace( array( '|', '=' ), ' ', wp_strip_all_tags( (string) $value ) ) );
	};

	$settings = array(
		'title=' . $clean( $title ),
		'button=' . $clean( $button ),
		'reset=' . $clean( $reset ),
	);

	return '[eazymatch view="searchjobs" settings="' . esc_attr( implode( '|', $settings ) ) . '"]';
}

/**
 * URL for the header call-to-action button.
 *
 * @return string
 */
function emr_header_cta_url() {
	$url = emr_option( 'emr_header_cta_url' );

	return '' !== $url ? $url : emr_jobs_url();
}

/**
 * URL for the homepage call-to-action button.
 *
 * @return string
 */
function emr_cta_url() {
	$url = emr_option( 'emr_cta_button_url' );

	if ( '' !== $url ) {
		return $url;
	}

	$free = get_option( 'emol_apply_url_free' );

	if ( ! empty( $free ) ) {
		return home_url( '/' . trim( (string) $free, '/' ) . '/' );
	}

	return emr_jobs_url();
}

/**
 * Parse the "value | label" lines from the hero statistics setting.
 *
 * @return array<int, array{value: string, label: string}>
 */
function emr_hero_stats() {
	$raw = trim( emr_option( 'emr_hero_stats' ) );

	if ( '' === $raw ) {
		return array();
	}

	$stats = array();

	foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
		$line = trim( wp_strip_all_tags( $line ) );

		if ( '' === $line ) {
			continue;
		}

		$parts = array_map( 'trim', explode( '|', $line, 2 ) );

		$stats[] = array(
			'value' => $parts[0],
			'label' => isset( $parts[1] ) ? $parts[1] : '',
		);
	}

	return $stats;
}

/**
 * Attachment id of the logo set under EazyMatch > EazyTheme, or the Customizer.
 *
 * @return int
 */
function emr_logo_id() {
	$plugin_logo = absint( get_option( 'emol_theme_logo_id' ) );

	if ( $plugin_logo ) {
		return $plugin_logo;
	}

	return absint( get_theme_mod( 'custom_logo' ) );
}

/**
 * Print the site logo or, when none is set, the site title.
 */
function emr_branding() {
	$logo_id = emr_logo_id();

	if ( $logo_id ) {
		printf(
			'<div class="emr-branding__logo"><a href="%s" rel="home">%s</a></div>',
			esc_url( home_url( '/' ) ),
			wp_get_attachment_image(
				$logo_id,
				'full',
				false,
				array(
					'class' => 'custom-logo',
					'alt'   => get_bloginfo( 'name', 'display' ),
				)
			)
		);

		return;
	}

	$tag = ( is_front_page() && ! is_paged() ) ? 'h1' : 'p';

	printf(
		'<%1$s class="emr-branding__title"><a href="%2$s" rel="home">%3$s</a></%1$s>',
		esc_attr( $tag ),
		esc_url( home_url( '/' ) ),
		esc_html( get_bloginfo( 'name' ) )
	);

	$tagline = get_bloginfo( 'description', 'display' );

	if ( $tagline ) {
		echo '<p class="emr-branding__tagline">' . esc_html( $tagline ) . '</p>';
	}
}

/**
 * Vacaturetitel van de huidige detailpagina, of een lege string.
 *
 * De plugin haalt de vacature al op voor de documenttitel (wp_head);
 * die data hergebruiken we hier.
 *
 * @return string
 */
function emr_current_job_title() {
	global $emol_job, $jobInfo;

	if ( is_array( $emol_job ) && ! empty( $emol_job['job']['name'] ) ) {
		return (string) $emol_job['job']['name'];
	}

	if ( is_array( $jobInfo ) && ! empty( $jobInfo['name'] ) ) {
		return (string) $jobInfo['name'];
	}

	return '';
}

/**
 * Heading for the grey page hero.
 *
 * On a job detail page this is the vacancy name, not the WordPress page title.
 *
 * @return string
 */
function emr_page_heading() {
	if ( is_page() && emr_page_has_eazymatch_view( get_the_ID(), 'job' ) ) {
		$job_title = emr_current_job_title();

		if ( '' !== $job_title ) {
			return $job_title;
		}
	}

	return get_the_title();
}

/**
 * Simple breadcrumb trail.
 */
function emr_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	echo '<nav class="emr-breadcrumbs" aria-label="' . esc_attr__( 'Kruimelpad', 'eazymatch-recruitment' ) . '">';
	printf(
		'<span><a href="%s">%s</a></span>',
		esc_url( home_url( '/' ) ),
		esc_html__( 'Home', 'eazymatch-recruitment' )
	);

	if ( is_singular() ) {
		$post = get_post();

		if ( $post instanceof WP_Post && $post->post_parent ) {
			printf(
				'<span><a href="%s">%s</a></span>',
				esc_url( (string) get_permalink( $post->post_parent ) ),
				esc_html( get_the_title( $post->post_parent ) )
			);
		}

		if ( 'post' === get_post_type() ) {
			$blog = get_option( 'page_for_posts' );

			if ( $blog ) {
				printf(
					'<span><a href="%s">%s</a></span>',
					esc_url( (string) get_permalink( $blog ) ),
					esc_html( get_the_title( $blog ) )
				);
			}
		}

		$heading = emr_page_heading();

		if ( '' !== emr_current_job_title() ) {
			echo '<span>' . esc_html( $heading ) . '</span>';
		} else {
			echo '<span>' . esc_html( wp_trim_words( $heading, 8, '&hellip;' ) ) . '</span>';
		}
	} elseif ( is_search() ) {
		echo '<span>' . esc_html__( 'Zoekresultaten', 'eazymatch-recruitment' ) . '</span>';
	} elseif ( is_404() ) {
		echo '<span>' . esc_html__( 'Pagina niet gevonden', 'eazymatch-recruitment' ) . '</span>';
	} elseif ( is_archive() ) {
		echo '<span>' . esc_html( wp_strip_all_tags( get_the_archive_title() ) ) . '</span>';
	}

	echo '</nav>';
}

/**
 * Publication date and author for a post.
 */
function emr_post_meta() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	printf(
		'<p class="emr-post-card__meta"><time datetime="%1$s">%2$s</time> &middot; %3$s</p>',
		esc_attr( (string) get_the_date( DATE_W3C ) ),
		esc_html( (string) get_the_date() ),
		esc_html( (string) get_the_author() )
	);
}

/**
 * Pagination for archives.
 */
function emr_pagination() {
	$links = paginate_links(
		array(
			'type'      => 'list',
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
		)
	);

	if ( ! $links ) {
		return;
	}

	echo '<nav class="emr-pagination" aria-label="' . esc_attr__( 'Paginering', 'eazymatch-recruitment' ) . '">';
	echo wp_kses_post( $links );
	echo '</nav>';
}
