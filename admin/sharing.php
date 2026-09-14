<?php
function eazymatch_plugin_sharing() {
	if ( ! get_option( 'emol_apihash' ) ) {
		wp_die( __( 'No eazymatch connection active.' ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	$allowed = emol_sharing::optionKeys();
	$hidden  = 'mt_submit_hidden';

	if ( isset( $_POST[ $hidden ] ) && $_POST[ $hidden ] === 'Y' ) {
		foreach ( $allowed as $option => $_keep ) {
			$value = isset( $_POST[ $option ] ) && (string) $_POST[ $option ] === '1' ? '1' : '0';
			update_option( $option, $value );
		}
		echo '<div class="updated"><p><strong>' . esc_html( EMOL_ADMIN_SAVED ) . '</strong></p></div>';
	}

	echo '<div class="wrap">';
	echo '<h2>' . esc_html( 'EazyMatch > ' . EMOL_ADMIN_SETTINGS . ' > ' . EMOL_ADMIN_SHARING ) . '</h2>';

	$boards = array(
		array(
			'option' => 'emol_sharing_indeed',
			'name'   => 'Indeed',
			'slug'   => 'indeed',
			'docs'   => 'https://developer.indeed.com/docs/indeed-apply/xml-job-feed/',
			'note'   => 'XML-feed voor publicatie op Indeed. Directe werkgevers gebruiken nog steeds deze feed; de Job Sync API is alleen voor ATS-partners.',
		),
		array(
			'option' => 'emol_sharing_jooble',
			'name'   => 'Jooble',
			'slug'   => 'jooble',
			'docs'   => 'https://jooble.org/advertisers',
			'note'   => 'XML-feed voor de Jooble-aggregator.',
		),
		array(
			'option' => 'emol_sharing_adzuna',
			'name'   => 'Adzuna',
			'slug'   => 'adzuna',
			'docs'   => 'https://www.adzuna.nl',
			'note'   => 'XML-feed voor Adzuna. Aanmelden als advertiser via Adzuna.',
		),
		array(
			'option' => 'emol_sharing_trovit',
			'name'   => 'Trovit',
			'slug'   => 'trovit',
			'docs'   => 'https://www.trovit.nl',
			'note'   => 'XML-feed voor Trovit (Lifull Connect).',
		),
	);

	$own_feeds = array(
		array(
			'option' => 'emol_sharing_rss',
			'name'   => 'RSS',
			'slug'   => 'rss',
			'note'   => 'Korte vacaturefeed voor feedreaders en automatische doorplaatsing.',
		),
		array(
			'option' => 'emol_sharing_rssfull',
			'name'   => 'RSS (volledig)',
			'slug'   => 'rssfull',
			'note'   => 'Zelfde als RSS, maar met volledige tekstblokken.',
		),
		array(
			'option' => 'emol_sharing_atom',
			'name'   => 'Atom',
			'slug'   => 'atom',
			'note'   => 'Atom 1.0-feed, bruikbaar als RSS-alternatief.',
		),
		array(
			'option' => 'emol_sharing_json',
			'name'   => 'JSON',
			'slug'   => 'json',
			'note'   => 'Machineleesbare JSON-lijst van gepubliceerde vacatures.',
		),
		array(
			'option' => 'emol_sharing_sitemap',
			'name'   => 'Sitemap (XML)',
			'slug'   => 'sitemap',
			'note'   => 'XML-sitemap van vacature-URL\'s voor zoekmachines.',
		),
	);

	?>
    <form method="post" action="">
        <input type="hidden" name="<?php echo esc_attr( $hidden ); ?>" value="Y">

        <div id="emol-admin-table">
            <h2>Delen op de vacaturepagina</h2>
            <p>Bezoekers delen een vacature via de officiële deelschermen van LinkedIn, Facebook en X (inloggen daar via OAuth), plus WhatsApp, e-mail en het systeemplatform van het apparaat.</p>
            <table class="emol-welcome-panel" style="width:100%;">
                <tr>
                    <td>Deelknoppen</td>
                    <td><?php eazymatch_sharing_on_off( 'emol_sharing_links' ); ?></td>
                </tr>
            </table>

            <h2>Google for Jobs</h2>
            <p>Zet een <code>JobPosting</code> (JSON-LD) op de vacaturepagina, zodat Google de vacature kan opnemen in Google for Jobs. Geen Google-account of OAuth nodig.</p>
            <table class="emol-welcome-panel" style="width:100%;">
                <tr>
                    <td>Structured data</td>
                    <td><?php eazymatch_sharing_on_off( 'emol_sharing_googlejobs' ); ?></td>
                </tr>
            </table>

            <h2>Vacaturefeeds voor jobboards</h2>
            <p>Lever een XML-feed aan het jobboard. SimplyHired, Uitzendbureau.nl en Twitterfeed zijn verwijderd; die programma's bestaan niet meer.</p>
            <div class="emol-sharing-grid">
				<?php foreach ( $boards as $board ) : ?>
					<?php eazymatch_sharing_feed_card( $board ); ?>
				<?php endforeach; ?>
            </div>

            <h2>Eigen feeds</h2>
            <div class="emol-sharing-grid">
				<?php foreach ( $own_feeds as $feed ) : ?>
					<?php eazymatch_sharing_feed_card( $feed ); ?>
				<?php endforeach; ?>
            </div>
        </div>

        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( EMOL_ACCOUNT_SAVE ); ?>"/>
        </p>
    </form>
	<?php
	echo '</div>';
}

/**
 * On/off radios for a sharing option.
 *
 * @param string $option
 */
function eazymatch_sharing_on_off( $option ) {
	$on = emol_sharing::isOn( $option );
	echo '<label><input type="radio" name="' . esc_attr( $option ) . '" value="1"' . ( $on ? ' checked="checked"' : '' ) . ' /> ' . esc_html( EMOL_ON ) . '</label> ';
	echo '<label><input type="radio" name="' . esc_attr( $option ) . '" value="0"' . ( $on ? '' : ' checked="checked"' ) . ' /> ' . esc_html( EMOL_OFF ) . '</label>';
}

/**
 * Feed card with toggle, URL and optional docs.
 *
 * @param array $feed
 */
function eazymatch_sharing_feed_card( $feed ) {
	$url = emol_sharing::feedUrl( $feed['slug'] );
	?>
    <div class="emol-sharing-card">
        <div class="emol-sharing-card__head">
            <strong><?php echo esc_html( $feed['name'] ); ?></strong>
			<?php eazymatch_sharing_on_off( $feed['option'] ); ?>
        </div>
		<?php if ( ! empty( $feed['note'] ) ) : ?>
            <p><?php echo esc_html( $feed['note'] ); ?></p>
		<?php endif; ?>
        <p class="emol-sharing-card__url">
            <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $url ); ?></a>
        </p>
		<?php if ( ! empty( $feed['docs'] ) ) : ?>
            <p><a href="<?php echo esc_url( $feed['docs'] ); ?>" target="_blank" rel="noopener noreferrer">Documentatie</a></p>
		<?php endif; ?>
    </div>
	<?php
}
