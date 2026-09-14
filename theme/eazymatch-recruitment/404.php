<?php
/**
 * 404 page.
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
		<h1><?php esc_html_e( 'Deze pagina bestaat niet (meer)', 'eazymatch-recruitment' ); ?></h1>
		<p class="emr-page-hero__meta"><?php esc_html_e( 'Mogelijk is de vacature vervallen of is de link verouderd.', 'eazymatch-recruitment' ); ?></p>
	</div>
</div>

<div class="emr-container">
	<div class="emr-content-area">
		<div>
			<p>
				<a class="emr-button" href="<?php echo esc_url( emr_jobs_url() ); ?>">
					<?php esc_html_e( 'Bekijk alle vacatures', 'eazymatch-recruitment' ); ?>
				</a>
				<a class="emr-button emr-button--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Naar de homepage', 'eazymatch-recruitment' ); ?>
				</a>
			</p>

			<div style="margin-top:2.5rem;max-width:520px;">
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
