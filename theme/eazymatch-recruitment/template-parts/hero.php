<?php
/**
 * Homepage hero, optionally with the EazyMatch job search form.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$emr_image  = emr_option( 'emr_hero_image' );
$emr_stats  = emr_hero_stats();
$emr_button = emr_option( 'emr_hero_button_text' );
$emr_url    = emr_option( 'emr_hero_button_url' );
$emr_url    = '' !== $emr_url ? $emr_url : emr_jobs_url();
?>
<section class="emr-hero<?php echo $emr_image ? ' emr-hero--image' : ''; ?>"
	<?php if ( $emr_image ) : ?>
		style="background-image:url('<?php echo esc_url( $emr_image ); ?>');"
	<?php endif; ?>
>
	<div class="emr-container">
		<div class="emr-hero__inner">
			<?php if ( '' !== emr_option( 'emr_hero_eyebrow' ) ) : ?>
				<span class="emr-eyebrow"><?php echo esc_html( emr_option( 'emr_hero_eyebrow' ) ); ?></span>
			<?php endif; ?>

			<h1><?php echo esc_html( emr_option( 'emr_hero_title' ) ); ?></h1>

			<?php if ( '' !== emr_option( 'emr_hero_text' ) ) : ?>
				<p class="emr-hero__lead"><?php echo wp_kses_post( emr_option( 'emr_hero_text' ) ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== $emr_button ) : ?>
				<div class="emr-hero__actions">
					<a class="emr-button emr-button--light" href="<?php echo esc_url( $emr_url ); ?>">
						<?php echo esc_html( $emr_button ); ?>
					</a>
					<a class="emr-button emr-button--outline-light" href="#vacatures">
						<?php esc_html_e( 'Actuele vacatures', 'eazymatch-recruitment' ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( '1' === emr_option( 'emr_hero_show_search' ) ) : ?>
		<div class="emr-container emr-hero__search">
			<?php
			echo emr_eazymatch( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				emr_searchjobs_shortcode(
					'',
					__( 'Zoeken', 'eazymatch-recruitment' ),
					__( 'Wis filters', 'eazymatch-recruitment' )
				)
			);
			?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $emr_stats ) ) : ?>
		<div class="emr-container">
			<div class="emr-hero__stats">
				<?php foreach ( $emr_stats as $emr_stat ) : ?>
					<div class="emr-hero__stat">
						<strong><?php echo esc_html( $emr_stat['value'] ); ?></strong>
						<span><?php echo esc_html( $emr_stat['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
