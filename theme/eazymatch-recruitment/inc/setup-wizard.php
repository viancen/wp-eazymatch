<?php
/**
 * One click setup: creates the pages the EazyMatch plugin needs and links them
 * in the plugin settings.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Blueprint of the pages this theme sets up.
 *
 * Each entry: slug, title, content and the EazyMatch option that must point at it.
 *
 * @return array<string, array<string, string>>
 */
function emr_setup_pages() {
	return array(
		'jobs'   => array(
			'slug'    => 'vacatures',
			'title'   => __( 'Vacatures', 'eazymatch-recruitment' ),
			'content' => emr_searchjobs_shortcode(
				__( 'Zoek een vacature', 'eazymatch-recruitment' ),
				__( 'Zoeken', 'eazymatch-recruitment' ),
				__( 'Alle vacatures tonen', 'eazymatch-recruitment' )
			) . "\n\n" . '[eazymatch view="jobpage"]',
			'option'  => 'emol_job_search_page',
			'dummy'   => 'emol_job_search_url',
			'dummy_v' => 'vacatures-zoeken',
		),
		'job'    => array(
			'slug'    => 'vacature',
			'title'   => __( 'Vacature', 'eazymatch-recruitment' ),
			'content' => '[eazymatch view="job"]',
			'option'  => 'emol_job_page',
			'dummy'   => 'emol_job_url',
			'dummy_v' => 'vacature-detail',
		),
		'apply'  => array(
			'slug'    => 'solliciteren',
			'title'   => __( 'Solliciteren', 'eazymatch-recruitment' ),
			'content' => '[eazymatch view="apply"]',
			'option'  => 'emol_apply_page',
			'dummy'   => 'emol_apply_url',
			'dummy_v' => 'reageren-vacature',
		),
		'thanks' => array(
			'slug'    => 'bedankt',
			'title'   => __( 'Bedankt voor je sollicitatie', 'eazymatch-recruitment' ),
			'content' => __( 'Bedankt voor je sollicitatie. We hebben je gegevens ontvangen en nemen zo snel mogelijk contact met je op.', 'eazymatch-recruitment' ),
			'option'  => 'emol_apply_url_success_redirect',
			'dummy'   => '',
			'dummy_v' => '',
		),
	);
}

/**
 * Which of the required pages already exist and are linked?
 *
 * @return array<string, array<string, mixed>>
 */
function emr_setup_status() {
	$status = array();

	foreach ( emr_setup_pages() as $key => $page ) {
		$existing = get_page_by_path( $page['slug'] );
		$linked   = get_option( $page['option'] );

		$status[ $key ] = array(
			'page'     => $page,
			'post'     => $existing,
			'exists'   => $existing instanceof WP_Post,
			'linked'   => ! empty( $linked ) && $existing instanceof WP_Post && $linked === $existing->post_name,
			'linkedto' => $linked,
		);
	}

	return $status;
}

/**
 * Is the setup complete?
 *
 * @return bool
 */
function emr_setup_complete() {
	foreach ( emr_setup_status() as $item ) {
		if ( ! $item['exists'] || ! $item['linked'] ) {
			return false;
		}
	}

	return true;
}

/**
 * Create missing pages and link them in the EazyMatch settings.
 *
 * @return array{created: string[], linked: string[]}
 */
function emr_run_setup() {
	$created = array();
	$linked  = array();

	foreach ( emr_setup_pages() as $page ) {
		$existing = get_page_by_path( $page['slug'] );

		if ( ! $existing instanceof WP_Post ) {
			$page_id = wp_insert_post(
				array(
					'post_title'     => $page['title'],
					'post_name'      => $page['slug'],
					'post_content'   => $page['content'],
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
				)
			);

			if ( is_wp_error( $page_id ) || ! $page_id ) {
				continue;
			}

			$existing  = get_post( $page_id );
			$created[] = $page['title'];
		}

		if ( ! $existing instanceof WP_Post ) {
			continue;
		}

		if ( get_option( $page['option'] ) !== $existing->post_name ) {
			update_option( $page['option'], $existing->post_name );
			$linked[] = $page['title'];
		}

		// Virtual fallback urls used by the plugin for old links, widgets and feeds.
		if ( ! empty( $page['dummy'] ) && empty( get_option( $page['dummy'] ) ) ) {
			update_option( $page['dummy'], $page['dummy_v'] );
		}
	}

	if ( empty( get_option( 'emol_apply_url_free' ) ) ) {
		update_option( 'emol_apply_url_free', 'open-sollicitatie' );
	}

	// The job cards in this theme look best with a short description and location.
	foreach ( array( 'emol_job_search_desc', 'emol_job_search_region', 'emol_job_search_city' ) as $option ) {
		if ( '' === (string) get_option( $option, '' ) ) {
			update_option( $option, 1 );
		}
	}

	if ( ! is_numeric( get_option( 'emol_job_amount_pp' ) ) || get_option( 'emol_job_amount_pp' ) < 1 ) {
		update_option( 'emol_job_amount_pp', 10 );
	}

	flush_rewrite_rules();

	return array(
		'created' => $created,
		'linked'  => $linked,
	);
}

/**
 * Handle the form on the setup screen.
 */
function emr_handle_setup_submit() {
	if ( ! isset( $_POST['emr_setup_nonce'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['emr_setup_nonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'emr_setup' ) ) {
		return;
	}

	$result = emr_run_setup();

	if ( ! empty( $_POST['emr_set_front_page'] ) ) {
		$front = get_page_by_path( 'home' );

		if ( ! $front instanceof WP_Post ) {
			$front_id = wp_insert_post(
				array(
					'post_title'   => __( 'Home', 'eazymatch-recruitment' ),
					'post_name'    => 'home',
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_content' => '',
				)
			);

			$front = is_wp_error( $front_id ) ? null : get_post( $front_id );
		}

		if ( $front instanceof WP_Post ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $front->ID );
		}
	}

	set_transient( 'emr_setup_result', $result, 60 );

	wp_safe_redirect( admin_url( 'admin.php?page=emol-theme&done=1' ) );
	exit;
}
add_action( 'admin_init', 'emr_handle_setup_submit' );

/**
 * Render the page-setup block (used on EazyMatch > EazyTheme).
 */
function emr_render_setup_section() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$status    = emr_setup_status();
	$permalink = get_option( 'permalink_structure' );
	$result    = get_transient( 'emr_setup_result' );

	if ( $result ) {
		delete_transient( 'emr_setup_result' );
	}
	?>
	<div class="emol-card">
		<h2><?php esc_html_e( 'Pagina\'s aanmaken', 'eazymatch-recruitment' ); ?></h2>

		<p>
			<?php esc_html_e( 'Dit thema gebruikt de shortcodes van de EazyMatch-plugin. Hieronder maak je in één keer de vier pagina\'s aan die de plugin nodig heeft en koppel je ze aan de juiste instellingen.', 'eazymatch-recruitment' ); ?>
		</p>

		<?php if ( $result ) : ?>
			<div class="notice notice-success">
				<p>
					<?php
					if ( ! empty( $result['created'] ) ) {
						printf(
							/* translators: %s: comma separated page titles. */
							esc_html__( 'Aangemaakt: %s.', 'eazymatch-recruitment' ),
							esc_html( implode( ', ', $result['created'] ) )
						);
						echo ' ';
					}

					if ( ! empty( $result['linked'] ) ) {
						printf(
							/* translators: %s: comma separated page titles. */
							esc_html__( 'Gekoppeld: %s.', 'eazymatch-recruitment' ),
							esc_html( implode( ', ', $result['linked'] ) )
						);
						echo ' ';
					}

					esc_html_e( 'De permalinks zijn opnieuw opgeslagen.', 'eazymatch-recruitment' );
					?>
				</p>
			</div>
		<?php endif; ?>

		<?php if ( ! emr_plugin_active() ) : ?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'De EazyMatch-plugin is niet actief. Installeer en activeer de plugin eerst; zonder plugin blijven de vacatureonderdelen leeg.', 'eazymatch-recruitment' ); ?></p>
			</div>
		<?php endif; ?>

		<?php if ( empty( $permalink ) ) : ?>
			<div class="notice notice-warning">
				<p>
					<?php
					printf(
						/* translators: %s: link to the permalink settings. */
						esc_html__( 'De permalinkstructuur staat op "Normaal". EazyMatch heeft leesbare urls nodig. Kies %s bijvoorbeeld "Berichtnaam".', 'eazymatch-recruitment' ),
						'<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '">' . esc_html__( 'bij Instellingen > Permalinks', 'eazymatch-recruitment' ) . '</a>'
					);
					?>
				</p>
			</div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width:900px;margin:20px 0;">
			<thead>
			<tr>
				<th><?php esc_html_e( 'Pagina', 'eazymatch-recruitment' ); ?></th>
				<th><?php esc_html_e( 'Inhoud', 'eazymatch-recruitment' ); ?></th>
				<th><?php esc_html_e( 'Status', 'eazymatch-recruitment' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $status as $item ) : ?>
				<tr>
					<td><strong><?php echo esc_html( $item['page']['title'] ); ?></strong><br><code>/<?php echo esc_html( $item['page']['slug'] ); ?></code></td>
					<td><code><?php echo esc_html( str_replace( "\n\n", ' ', $item['page']['content'] ) ); ?></code></td>
					<td>
						<?php if ( $item['exists'] && $item['linked'] ) : ?>
							<span style="color:#1a7f37;">&#10003; <?php esc_html_e( 'Aangemaakt en gekoppeld', 'eazymatch-recruitment' ); ?></span>
						<?php elseif ( $item['exists'] ) : ?>
							<span style="color:#996800;">&#9888; <?php esc_html_e( 'Pagina bestaat, nog niet gekoppeld', 'eazymatch-recruitment' ); ?></span>
						<?php else : ?>
							<span style="color:#8a1f11;">&#10007; <?php esc_html_e( 'Ontbreekt', 'eazymatch-recruitment' ); ?></span>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<form method="post" action="">
			<?php wp_nonce_field( 'emr_setup', 'emr_setup_nonce' ); ?>

			<p>
				<label>
					<input type="checkbox" name="emr_set_front_page" value="1" <?php checked( 'page' !== get_option( 'show_on_front' ) ); ?> />
					<?php esc_html_e( 'Ook een statische homepage instellen (pagina "Home" met de vacature-homepage van dit thema).', 'eazymatch-recruitment' ); ?>
				</label>
			</p>

			<p>
				<button type="submit" class="button button-primary">
					<?php
					echo emr_setup_complete()
						? esc_html__( 'Instellingen opnieuw toepassen', 'eazymatch-recruitment' )
						: esc_html__( 'Pagina\'s aanmaken en koppelen', 'eazymatch-recruitment' );
					?>
				</button>
			</p>
		</form>

		<h2><?php esc_html_e( 'Daarna', 'eazymatch-recruitment' ); ?></h2>
		<ol>
			<li><?php esc_html_e( 'Vul onder EazyMatch de API-gegevens in en maak verbinding.', 'eazymatch-recruitment' ); ?></li>
			<li><?php esc_html_e( 'Stel hierboven de primaire kleur en het logo in. Hero-teksten staan onder Weergave > Customizer.', 'eazymatch-recruitment' ); ?></li>
			<li><?php esc_html_e( 'Maak onder Weergave > Menu\'s een hoofdmenu en koppel dat aan de menupositie "Hoofdmenu".', 'eazymatch-recruitment' ); ?></li>
			<li><?php esc_html_e( 'Sluit de sollicitatiepagina uit van paginacache.', 'eazymatch-recruitment' ); ?></li>
		</ol>
	</div>
	<?php
}

/**
 * Nudge administrators towards the setup screen while it is incomplete.
 */
function emr_setup_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( $screen && isset( $_GET['page'] ) && 'emol-theme' === $_GET['page'] ) {
		return;
	}

	if ( emr_setup_complete() ) {
		return;
	}
	?>
	<div class="notice notice-info is-dismissible">
		<p>
			<strong><?php esc_html_e( 'EazyMatch Recruitment', 'eazymatch-recruitment' ); ?></strong> &mdash;
			<?php esc_html_e( 'De vacaturepagina\'s van dit thema zijn nog niet volledig ingesteld.', 'eazymatch-recruitment' ); ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=emol-theme' ) ); ?>"><?php esc_html_e( 'Installatie afronden', 'eazymatch-recruitment' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'emr_setup_notice' );
