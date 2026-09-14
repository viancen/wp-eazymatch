<?php
/**
 * WordPress search results. Vacatures worden gezocht via het EazyMatch-formulier.
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
		<h1>
			<?php
			printf(
				/* translators: %s: search term. */
				esc_html__( 'Zoekresultaten voor %s', 'eazymatch-recruitment' ),
				'&ldquo;' . esc_html( get_search_query() ) . '&rdquo;'
			);
			?>
		</h1>
		<p class="emr-page-hero__meta"><?php esc_html_e( 'Zoek je een vacature? Gebruik het vacaturezoekformulier op de vacaturepagina.', 'eazymatch-recruitment' ); ?></p>
	</div>
</div>

<div class="emr-container">
	<div class="emr-content-area">
		<div>
			<?php if ( have_posts() ) : ?>
				<div class="emr-posts">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					?>
				</div>

				<?php emr_pagination(); ?>
			<?php else : ?>
				<div class="emr-no-results">
					<h2><?php esc_html_e( 'Niets gevonden', 'eazymatch-recruitment' ); ?></h2>
					<p><?php esc_html_e( 'Probeer een andere zoekterm, of bekijk direct het vacatureaanbod.', 'eazymatch-recruitment' ); ?></p>
					<?php get_search_form(); ?>
					<p style="margin-top:1.5rem;">
						<a class="emr-button" href="<?php echo esc_url( emr_jobs_url() ); ?>">
							<?php esc_html_e( 'Naar de vacatures', 'eazymatch-recruitment' ); ?>
						</a>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php
get_footer();
