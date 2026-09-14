<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * EazyTheme: appearance settings for the bundled recruitment theme.
 *
 * Stores the primary colour and logo as plugin options so they survive a
 * theme switch. The recruitment theme reads these values first.
 */

/**
 * Default brand colour used by the recruitment theme.
 */
if ( ! defined( 'EMOL_THEME_DEFAULT_COLOR' ) ) {
	define( 'EMOL_THEME_DEFAULT_COLOR', '#1f5eff' );
}

/**
 * Handle the appearance form on EazyTheme.
 */
function eazymatch_theme_handle_submit() {
	if ( ! isset( $_POST['emol_theme_nonce'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['emol_theme_nonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'emol_theme_save' ) ) {
		return;
	}

	$color = isset( $_POST['emol_theme_brand_color'] )
		? sanitize_hex_color( wp_unslash( $_POST['emol_theme_brand_color'] ) )
		: '';

	if ( empty( $color ) ) {
		$color = EMOL_THEME_DEFAULT_COLOR;
	}

	update_option( 'emol_theme_brand_color', $color );

	if ( function_exists( 'set_theme_mod' ) ) {
		set_theme_mod( 'emr_brand_color', $color );
	}

	if ( isset( $_POST['emol_theme_logo_id'] ) ) {
		$logo_id = absint( $_POST['emol_theme_logo_id'] );
		update_option( 'emol_theme_logo_id', $logo_id );

		if ( function_exists( 'set_theme_mod' ) ) {
			if ( $logo_id ) {
				set_theme_mod( 'custom_logo', $logo_id );
			} elseif ( function_exists( 'remove_theme_mod' ) ) {
				remove_theme_mod( 'custom_logo' );
			}
		}
	}

	set_transient( 'emol_theme_saved', 1, 60 );

	wp_safe_redirect( admin_url( 'admin.php?page=emol-theme&saved=1' ) );
	exit;
}

add_action( 'admin_init', 'eazymatch_theme_handle_submit' );

/**
 * Render the EazyTheme admin screen.
 */
function eazymatch_plugin_theme() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	$color   = get_option( 'emol_theme_brand_color', EMOL_THEME_DEFAULT_COLOR );
	$logo_id = absint( get_option( 'emol_theme_logo_id' ) );

	if ( empty( $color ) ) {
		$color = EMOL_THEME_DEFAULT_COLOR;
	}

	$logo_src = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
	$saved    = get_transient( 'emol_theme_saved' );

	if ( $saved ) {
		delete_transient( 'emol_theme_saved' );
	}

	$theme_active = function_exists( 'emr_setup_pages' );
	?>
	<div class="wrap">
		<h2><?php echo esc_html( EMOL_ADMIN_THEME ); ?></h2>

		<?php if ( $saved ) : ?>
			<div class="updated"><p><strong><?php echo esc_html( EMOL_ADMIN_SAVED ); ?></strong></p></div>
		<?php endif; ?>

		<p>
			<?php esc_html_e( 'Instellingen voor het EazyMatch Recruitment-thema: primaire kleur en logo.', 'Emol-3.0-identifier' ); ?>
		</p>

		<form method="post" action="">
			<?php wp_nonce_field( 'emol_theme_save', 'emol_theme_nonce' ); ?>

			<div class="emol-card">
				<h2><?php esc_html_e( 'Vormgeving', 'Emol-3.0-identifier' ); ?></h2>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="emol_theme_brand_color"><?php esc_html_e( 'Primaire kleur', 'Emol-3.0-identifier' ); ?></label>
						</th>
						<td>
							<input type="text"
								name="emol_theme_brand_color"
								id="emol_theme_brand_color"
								class="emol-color-field"
								value="<?php echo esc_attr( $color ); ?>"
								data-default-color="<?php echo esc_attr( EMOL_THEME_DEFAULT_COLOR ); ?>" />
							<p class="description">
								<?php esc_html_e( 'Wordt gebruikt voor knoppen, links en de hero van het recruitmentthema.', 'Emol-3.0-identifier' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Logo', 'Emol-3.0-identifier' ); ?></th>
						<td>
							<div class="emol-theme-logo">
								<div class="emol-theme-logo__preview" id="emol-theme-logo-preview">
									<?php if ( $logo_src ) : ?>
										<img src="<?php echo esc_url( $logo_src ); ?>" alt="" />
									<?php endif; ?>
								</div>
								<input type="hidden" name="emol_theme_logo_id" id="emol_theme_logo_id" value="<?php echo esc_attr( (string) $logo_id ); ?>" />
								<p>
									<button type="button" class="button" id="emol-theme-logo-upload">
										<?php esc_html_e( 'Logo kiezen', 'Emol-3.0-identifier' ); ?>
									</button>
									<button type="button" class="button" id="emol-theme-logo-clear" <?php echo $logo_id ? '' : 'style="display:none"'; ?>>
										<?php esc_html_e( 'Logo verwijderen', 'Emol-3.0-identifier' ); ?>
									</button>
								</p>
							</div>
						</td>
					</tr>
				</table>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Opslaan' ); ?>" />
				</p>
			</div>
		</form>

		<?php if ( $theme_active && function_exists( 'emr_render_setup_section' ) ) : ?>
			<?php emr_render_setup_section(); ?>
		<?php elseif ( ! $theme_active ) : ?>
			<div class="emol-card">
				<h2><?php esc_html_e( 'Thema niet actief', 'Emol-3.0-identifier' ); ?></h2>
				<p>
					<?php esc_html_e( 'Activeer het EazyMatch Recruitment-thema onder Weergave > Thema\'s om de pagina-installatie te gebruiken. Kleur en logo worden alvast bewaard.', 'Emol-3.0-identifier' ); ?>
				</p>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
