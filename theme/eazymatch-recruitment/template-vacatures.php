<?php
/**
 * Template Name: Vacatureoverzicht (zoekformulier + resultaten)
 *
 * Plaatst het EazyMatch-zoekformulier en de zoekresultaten zelf, zodat er geen
 * shortcodes in de pagina-inhoud nodig zijn. Eigen tekst in de pagina wordt
 * boven het zoekformulier getoond.
 *
 * Koppel deze pagina onder EazyMatch > Vacatures als "Job zoek page".
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="emr-page-hero">
	<div class="emr-container">
		<?php emr_breadcrumbs(); ?>
		<h1><?php the_title(); ?></h1>
	</div>
</div>

<div class="emr-container">
	<div class="emr-content-area">
		<div>
			<?php
			while ( have_posts() ) :
				the_post();

				if ( '' !== trim( (string) get_the_content() ) ) {
					echo '<div class="emr-entry__content" style="margin-bottom:2rem;">';
					the_content();
					echo '</div>';
				}
			endwhile;

			echo emr_eazymatch( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				emr_searchjobs_shortcode(
					'',
					__( 'Zoeken', 'eazymatch-recruitment' ),
					__( 'Alle vacatures tonen', 'eazymatch-recruitment' )
				)
			);

			$emr_teaser    = trim( emr_option( 'emr_jobs_teaser_text' ) );
			$emr_shortcode = '' !== $emr_teaser
				? '[eazymatch view="jobpage" job-teaser-text="' . esc_attr( $emr_teaser ) . '"]'
				: '[eazymatch view="jobpage"]';

			echo '<div style="margin-top:2.5rem;">';
			echo emr_eazymatch( $emr_shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';
			?>
		</div>
	</div>
</div>

<?php
get_footer();
