<?php
/**
 * Shown when a query returns no posts.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="emr-no-results">
	<h2><?php esc_html_e( 'Nog geen berichten', 'eazymatch-recruitment' ); ?></h2>
	<p><?php esc_html_e( 'Er is hier nog niets gepubliceerd. Bekijk in de tussentijd het vacatureaanbod.', 'eazymatch-recruitment' ); ?></p>
	<p>
		<a class="emr-button" href="<?php echo esc_url( emr_jobs_url() ); ?>">
			<?php esc_html_e( 'Naar de vacatures', 'eazymatch-recruitment' ); ?>
		</a>
	</p>
</div>
