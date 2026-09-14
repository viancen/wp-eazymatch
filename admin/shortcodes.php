<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * EazyMatch > Shortcodes
 *
 * Overview of every [eazymatch] shortcode view and its parameters, generated
 * from emol_shortcoderegistry. When an API connection is available the text
 * blocks that can be used for job-teaser-text are listed as well.
 */
function eazymatch_plugin_shortcodes() {

	//must check that the user has the required capability
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	$views = emol_shortcoderegistry::getAll();

	// text block definitions (for job-teaser-text), only when connected
	$jobTexts      = null;
	$jobTextsError = '';
	if ( get_option( 'emol_apihash' ) && eazymatch_connect() ) {
		try {
			$trunk    = new emol_trunk();
			$jobTexts = &$trunk->request( 'form', 'getJobTextDescription' );
			$trunk->execute();
		} catch ( Exception $e ) {
			$jobTexts      = null;
			$jobTextsError = $e->getMessage();
		}
	}
	?>
    <div class="wrap">
        <h2>EazyMatch &ndash; Shortcodes</h2>

        <p>
            Alle onderdelen van de plugin worden op een pagina geplaatst met de shortcode <code>[eazymatch view="..."]</code>.
            Gebruik hiervoor een Gutenberg-blok van het type <strong>Shortcode</strong> (of de shortcode-widget van je pagebuilder);
            plak shortcodes niet in een HTML- of PHP-blok. Parameters zijn optioneel tenzij anders aangegeven.
        </p>

		<?php foreach ( $views as $view => $info ) : ?>
            <div class="emol-shortcode-card" style="background:#fff;border:1px solid #c3c4c7;box-shadow:0 1px 1px rgba(0,0,0,.04);padding:12px 16px;margin:16px 0;">
                <h3 style="margin:0 0 6px;">
                    <code style="font-size:14px;">[eazymatch view="<?php echo esc_html( $view ); ?>"]</code>
                </h3>
                <p style="margin:0 0 10px;"><?php echo esc_html( $info['description'] ); ?></p>

				<?php if ( ! empty( $info['params'] ) ) : ?>
                    <table class="widefat striped" style="margin-bottom:10px;">
                        <thead>
                        <tr>
                            <th style="width:16%;">Parameter</th>
                            <th style="width:16%;">Type</th>
                            <th style="width:8%;">Verplicht</th>
                            <th style="width:20%;">Standaard</th>
                            <th>Omschrijving</th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $info['params'] as $name => $param ) : ?>
                            <tr>
                                <td><code><?php echo esc_html( $name ); ?></code><br>
                                    <small style="color:#646970;">bijv. <code><?php echo esc_html( $param['example'] ); ?></code></small>
                                </td>
                                <td><?php echo esc_html( $param['type'] ); ?></td>
                                <td><?php echo $param['required'] ? '<strong>ja</strong>' : 'nee'; ?></td>
                                <td><?php echo esc_html( $param['default'] ); ?></td>
                                <td><?php echo esc_html( $param['description'] ); ?></td>
                            </tr>
						<?php endforeach; ?>
                        </tbody>
                    </table>
				<?php else : ?>
                    <p style="margin:0 0 10px;color:#646970;"><em>Deze shortcode heeft geen parameters.</em></p>
				<?php endif; ?>

				<?php if ( ! empty( $info['examples'] ) ) : ?>
                    <p style="margin:0 0 4px;"><strong>Voorbeelden</strong></p>
					<?php foreach ( $info['examples'] as $example ) : ?>
                        <p style="margin:0 0 4px;"><code><?php echo esc_html( $example ); ?></code></p>
					<?php endforeach; ?>
				<?php endif; ?>
            </div>
		<?php endforeach; ?>

        <h2 style="margin-top:28px;">Tekstblokken voor <code><?php echo esc_html( emol_shortcoderegistry::ATTR_JOB_TEASER_TEXT ); ?></code></h2>
        <p>
            Deze tekstblokken zijn in EazyMatch gedefinieerd voor vacatures. Gebruik het id (of de originele titel) als waarde van
            <code><?php echo esc_html( emol_shortcoderegistry::ATTR_JOB_TEASER_TEXT ); ?></code> bij <code>view="jobs"</code> of <code>view="jobpage"</code>.
            De titels die je onder <em>Vacatures &gt; Tekstblokken</em> hebt hernoemd, gelden alleen voor de weergave; hier telt de originele EazyMatch-titel.
        </p>

		<?php if ( ! get_option( 'emol_apihash' ) ) : ?>
            <p style="color:#b32d2e;"><strong>Geen EazyMatch-verbinding actief.</strong> Stel eerst de API-gegevens in onder EazyMatch &gt; Instellingen.</p>
		<?php elseif ( $jobTextsError !== '' ) : ?>
            <p style="color:#b32d2e;"><strong>Tekstblokken konden niet worden opgehaald:</strong> <?php echo esc_html( $jobTextsError ); ?></p>
		<?php elseif ( ! is_array( $jobTexts ) || count( $jobTexts ) === 0 ) : ?>
            <p><em>Er zijn geen tekstblokken gevonden.</em></p>
		<?php else : ?>
            <table class="widefat striped" style="max-width:700px;">
                <thead>
                <tr>
                    <th style="width:20%;">Id</th>
                    <th>Originele titel</th>
                    <th>Voorbeeld</th>
                </tr>
                </thead>
                <tbody>
				<?php
				$idField    = emol_jobteaser::ID_FIELD;
				$labelField = emol_jobteaser::LABEL_FIELD;
				$missingId  = false;
				foreach ( $jobTexts as $textBlock ) :
					if ( ! is_array( $textBlock ) ) {
						continue;
					}
					$id    = isset( $textBlock[ $idField ] ) ? (string) $textBlock[ $idField ] : '';
					$label = isset( $textBlock[ $labelField ] ) ? (string) $textBlock[ $labelField ] : '';
					if ( $id === '' ) {
						$missingId = true;
					}
					$reference = $id !== '' ? $id : $label;
					?>
                    <tr>
                        <td><?php echo $id !== '' ? '<code>' . esc_html( $id ) . '</code>' : '<em>&ndash;</em>'; ?></td>
                        <td><?php echo esc_html( $label ); ?></td>
                        <td><code>[eazymatch view="jobs" <?php echo esc_html( emol_shortcoderegistry::ATTR_JOB_TEASER_TEXT ); ?>="<?php echo esc_attr( $reference ); ?>"]</code></td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>
			<?php if ( $missingId ) : ?>
                <p style="color:#646970;">
                    <em>Niet elk tekstblok heeft een veld <code><?php echo esc_html( $idField ); ?></code> in de API-response; gebruik dan de originele titel als waarde.
                        Beschikbare velden in de response: <code><?php echo esc_html( implode( ', ', array_keys( (array) reset( $jobTexts ) ) ) ); ?></code>.</em>
                </p>
			<?php endif; ?>
		<?php endif; ?>
    </div>
	<?php
}
