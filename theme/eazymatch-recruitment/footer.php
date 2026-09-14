<?php
/**
 * Site footer.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main><!-- .emr-main -->

<footer class="emr-footer">
	<div class="emr-container">
		<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) ) : ?>
			<div class="emr-footer__widgets">
				<?php
				for ( $i = 1; $i <= 3; $i++ ) {
					if ( is_active_sidebar( 'footer-' . $i ) ) {
						echo '<div class="emr-footer__column">';
						dynamic_sidebar( 'footer-' . $i );
						echo '</div>';
					}
				}
				?>
			</div>
		<?php elseif ( has_nav_menu( 'footer' ) ) : ?>
			<div class="emr-footer__widgets">
				<div class="emr-footer__column">
					<h2><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h2>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
						)
					);
					?>
				</div>
			</div>
		<?php endif; ?>

		<div class="emr-footer__bottom">
			<p>
				<?php
				$footer_text = emr_option( 'emr_footer_text' );

				if ( '' !== $footer_text ) {
					echo wp_kses_post( $footer_text );
				} else {
					printf(
						/* translators: 1: year, 2: site name. */
						esc_html__( '&copy; %1$s %2$s', 'eazymatch-recruitment' ),
						esc_html( (string) gmdate( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
				}
				?>
			</p>

			<?php
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => false,
						'depth'          => 1,
					)
				);
			}
			?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
