<?php
/**
 * Recruitment homepage: hero with job search, a job list and a call to action.
 *
 * Any content added to the static front page is rendered between the job list
 * and the call to action.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part( 'template-parts/hero' );
?>

<section class="emr-section emr-section--alt" id="vacatures">
	<div class="emr-container">
		<div class="emr-section__head">
			<?php if ( '' !== emr_option( 'emr_jobs_title' ) ) : ?>
				<span class="emr-eyebrow"><?php esc_html_e( 'Vacatures', 'eazymatch-recruitment' ); ?></span>
				<h2><?php echo esc_html( emr_option( 'emr_jobs_title' ) ); ?></h2>
			<?php endif; ?>

			<?php if ( '' !== emr_option( 'emr_jobs_text' ) ) : ?>
				<p><?php echo wp_kses_post( emr_option( 'emr_jobs_text' ) ); ?></p>
			<?php endif; ?>
		</div>

		<?php echo emr_eazymatch( emr_jobs_shortcode() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<p style="margin-top:2rem;">
			<a class="emr-button emr-button--ghost" href="<?php echo esc_url( emr_jobs_url() ); ?>">
				<?php esc_html_e( 'Bekijk alle vacatures', 'eazymatch-recruitment' ); ?>
			</a>
		</p>
	</div>
</section>

<?php get_template_part( 'template-parts/steps' ); ?>

<?php
// Editable content of the page that is set as static homepage.
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		$content = get_the_content();

		if ( '' !== trim( (string) $content ) ) :
			?>
			<section class="emr-section">
				<div class="emr-container emr-entry__content">
					<?php the_content(); ?>
				</div>
			</section>
			<?php
		endif;
	endwhile;
endif;
?>

<?php if ( '' !== emr_option( 'emr_cta_title' ) ) : ?>
	<section class="emr-section">
		<div class="emr-container">
			<div class="emr-cta">
				<h2><?php echo esc_html( emr_option( 'emr_cta_title' ) ); ?></h2>

				<?php if ( '' !== emr_option( 'emr_cta_text' ) ) : ?>
					<p><?php echo wp_kses_post( emr_option( 'emr_cta_text' ) ); ?></p>
				<?php endif; ?>

				<?php if ( '' !== emr_option( 'emr_cta_button_text' ) ) : ?>
					<div class="emr-cta__actions">
						<a class="emr-button emr-button--light" href="<?php echo esc_url( emr_cta_url() ); ?>">
							<?php echo esc_html( emr_option( 'emr_cta_button_text' ) ); ?>
						</a>
						<a class="emr-button emr-button--outline-light" href="<?php echo esc_url( emr_jobs_url() ); ?>">
							<?php esc_html_e( 'Naar de vacatures', 'eazymatch-recruitment' ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
