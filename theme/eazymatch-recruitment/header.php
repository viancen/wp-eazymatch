<?php
/**
 * Site header.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'emr-site' ); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#emr-content"><?php esc_html_e( 'Naar de inhoud', 'eazymatch-recruitment' ); ?></a>

<header class="emr-header" id="emr-header">
	<div class="emr-container emr-header__inner">
		<div class="emr-branding">
			<?php emr_branding(); ?>
		</div>

		<nav class="emr-nav" aria-label="<?php esc_attr_e( 'Hoofdmenu', 'eazymatch-recruitment' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'depth'          => 2,
					)
				);
			} else {
				wp_page_menu(
					array(
						'menu_class' => '',
						'container'  => false,
						'before'     => '',
						'after'      => '',
					)
				);
			}
			?>
		</nav>

		<div class="emr-header__actions">
			<?php if ( '' !== emr_option( 'emr_header_cta_text' ) ) : ?>
				<a class="emr-button emr-header__cta" href="<?php echo esc_url( emr_header_cta_url() ); ?>">
					<?php echo esc_html( emr_option( 'emr_header_cta_text' ) ); ?>
				</a>
			<?php endif; ?>

			<button class="emr-header__toggle" type="button" aria-expanded="false" aria-controls="emr-mobile-nav">
				<span></span><span></span><span></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Menu openen', 'eazymatch-recruitment' ); ?></span>
			</button>
		</div>
	</div>

	<div class="emr-mobile-nav" id="emr-mobile-nav">
		<div class="emr-container">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'depth'          => 2,
					)
				);
			} else {
				wp_page_menu(
					array(
						'menu_class' => '',
						'container'  => false,
						'before'     => '',
						'after'      => '',
					)
				);
			}

			if ( '' !== emr_option( 'emr_header_cta_text' ) ) :
				?>
				<a class="emr-button" href="<?php echo esc_url( emr_header_cta_url() ); ?>">
					<?php echo esc_html( emr_option( 'emr_header_cta_text' ) ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</header>

<main class="emr-main" id="emr-content">
