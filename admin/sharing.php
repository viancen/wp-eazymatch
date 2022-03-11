<?php
function eazymatch_plugin_sharing()
{

	if (!get_option('emol_apihash')) {
		wp_die(__('No eazymatch connection active.'));

	}
	//must check that the user has the required capability
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}

	// variables for the field and option names
	$hidden_field_name = 'mt_submit_hidden';

	$eazymatchOptions = array(
		'emol_sharing_tiny' => get_option('emol_sharing_tiny'),
		'emol_sharing_indeed' => get_option('emol_sharing_indeed'),
		'emol_sharing_trovit' => get_option('emol_sharing_trovit'),
		'emol_sharing_jooble' => get_option('emol_sharing_jooble'),
		'emol_sharing_uitzendbureau' => get_option('emol_sharing_uitzendbureau'),
		'emol_sharing_simplyhired' => get_option('emol_sharing_simplyhired'),
		'emol_sharing_adzuna' => get_option('emol_sharing_adzuna'),
		'emol_sharing_rss' => get_option('emol_sharing_rss'),
		'emol_sharing_full' => get_option('emol_sharing_rssfull'),
		'emol_sharing_atom' => get_option('emol_sharing_atom'),
		'emol_sharing_json' => get_option('emol_sharing_json'),
		'emol_sharing_sitemap' => get_option('emol_sharing_sitemap'),
		'emol_sharing_links' => get_option('emol_sharing_links'),
		'emol_sharing_googlejobs' => get_option('emol_sharing_googlejobs'),
	);

	// If they did, this hidden field will be set to 'Y'
	if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {

		foreach ($_POST as $option => $value) {

			if (!get_option($option)) {
				add_option($option);
			}
			update_option($option, $value);

			$eazymatchOptions[$option] = $value;
		}

		?>
        <div class="updated"><p><strong><?php _e(EMOL_ADMIN_SAVED, 'Emol-3.0-identifier'); ?></strong></p></div>
		<?php
	}

	echo '<div class="wrap">';
	echo "<h2>" . __('EazyMatch > ' . EMOL_ADMIN_SETTINGS . ' > ' . EMOL_ADMIN_SHARING . ' ', 'Emol-3.0-identifier') . "</h2>";

	// settings form
	$sel2 = 'checked="checked"';
	$sel1 = '';
	$sel3 = '';
	if (get_option('emol_sharing_links') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
		$sel3 = '';
	}
	$checkboxSocialsharing = '<input type="radio" name="emol_sharing_links" value="1" ' . $sel1 . ' /> Sharewidget aan &nbsp;';
	$checkboxSocialsharing .= '<input type="radio" name="emol_sharing_links" value="0" ' . $sel3 . ' /> Sharewidget uit';

	// settings form
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_indeed') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxIndeed = '<input type="radio" name="emol_sharing_indeed" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxIndeed .= '<input type="radio" name="emol_sharing_indeed" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	//trovit
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_trovit') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxTrovit = '<input type="radio" name="emol_sharing_trovit" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxTrovit .= '<input type="radio" name="emol_sharing_trovit" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	//emol_sharing_jooble
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_jooble') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxJooble = '<input type="radio" name="emol_sharing_jooble" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxJooble .= '<input type="radio" name="emol_sharing_jooble" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	//emol_sharing_jooble
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_json') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxJson = '<input type="radio" name="emol_sharing_json" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxJson .= '<input type="radio" name="emol_sharing_json" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	//emol_sharing_uitzendbureau
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_uitzendbureau') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxUitzendbureau = '<input type="radio" name="emol_sharing_uitzendbureau" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxUitzendbureau .= '<input type="radio" name="emol_sharing_uitzendbureau" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	//emol_sharing_simplyhired
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_simplyhired') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxSimplyhired = '<input type="radio" name="emol_sharing_simplyhired" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxSimplyhired .= '<input type="radio" name="emol_sharing_simplyhired" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	//adzuna
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_adzuna') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxadzuna = '<input type="radio" name="emol_sharing_adzuna" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxadzuna .= '<input type="radio" name="emol_sharing_adzuna" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	//atom
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_atom') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxAtom = '<input type="radio" name="emol_sharing_atom" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxAtom .= '<input type="radio" name="emol_sharing_atom" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	//rss
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_rss') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxRss = '<input type="radio" name="emol_sharing_rss" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxRss .= '<input type="radio" name="emol_sharing_rss" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	//rss
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_tiny') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxTiny = '<input type="radio" name="emol_sharing_tiny" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxTiny .= '<input type="radio" name="emol_sharing_tiny" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	//emol_sharing_sitemap
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_sitemap') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxSitemap = '<input type="radio" name="emol_sharing_sitemap" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxSitemap .= '<input type="radio" name="emol_sharing_sitemap" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	//rssfull
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_rssfull') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxRssfull = '<input type="radio" name="emol_sharing_rssfull" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxRssfull .= '<input type="radio" name="emol_sharing_rssfull" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';


	//googlejobs
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if (get_option('emol_sharing_googlejobs') == 1) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxGoogle = '<input type="radio" name="emol_sharing_googlejobs" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxGoogle .= '<input type="radio" name="emol_sharing_googlejobs" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';


	?>
    <style>
        #emol-admin-table table tr td {
            border-bottom: solid 1px #efefef;
        }

        #emol-admin-table table tr td input[type="text"] {
            border: solid 1px #cacaca;
            width: 100%;
            padding: 4px !important;
        }
    </style>
    <form name="form1" method="post" action="">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

        <div id="emol-admin-table">
            <table class="emol-welcome-panel" style="width: 100%;">
                <tr>
                    <td><br/> EazyMatch Social sharing</td>
                    <td><br/> <?php echo $checkboxSocialsharing ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="cTdh"><br>
                        <h2>EazyMatch Multiposting</h2></td>
                </tr>
                <tr>
                    <td width="200">Google Jobs</td>
                    <td><?php echo $checkboxGoogle ?></td>
                </tr>
                <tr>
                    <td width="200">Indeed.com</td>
                    <td><?php echo $checkboxIndeed ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td bgcolor="white">
                        <a href="https://ads.indeed.com/?hl=nl" target="_new">
                            <img
                                    src="<?php echo bloginfo('url') ?>/wp-content/plugins/wp-eazymatch/assets/img/indeed-logo.png"
                                    style="width:100px;border: solid 1px #ccc;"/>
                        </a>
                        <br/>
                        <a href="<?php echo bloginfo('url') ?>/em-jobfeed/indeed"
                           target="_new"><?php echo bloginfo('url') ?>/em-jobfeed/indeed</a><br/>
                        specificaties: <a href="http://www.indeed.com/intl/en/xmlinfo.html">http://www.indeed.com/intl/en/xmlinfo.html</a>
                    </td>
                </tr>
                <tr>
                    <td width="200">Jooble.co.nl</td>
                    <td><?php echo $checkboxJooble ?></td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td bgcolor="white">
                        <a href="https://ads.indeed.com/?hl=nl" target="_new">
                            <img
                                    src="<?php echo bloginfo('url') ?>/wp-content/plugins/wp-eazymatch/assets/img/jooble-logo.gif"
                                    style="width:100px;border: solid 1px #ccc;"/>
                        </a>
                        <br/>
                        <a href="<?php echo bloginfo('url') ?>/em-jobfeed/jooble"
                           target="_new"><?php echo bloginfo('url') ?>/em-jobfeed/jooble</a><br/>
                        specificaties: <a href="http://jooble.co.nl/info/index" target="_blank">http://jooble.co.nl/info/index</a>
                    </td>
                </tr>
                <tr>
                    <td width="200">Uitzendbureau.nl</td>
                    <td><?php echo $checkboxUitzendbureau ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td bgcolor="white">
                        <a href="<?php echo bloginfo('url') ?>/em-jobfeed/uitzendbureau"
                           target="_new"><?php echo bloginfo('url') ?>/em-jobfeed/uitzendbureau</a><br/>
                        specificaties: <a href="http://developer.uitzendbureau.nl/jobs-xml/doc/sign-up.html"
                                          target="_blank">http://developer.uitzendbureau.nl/jobs-xml/doc/sign-up.html</a>
                    </td>
                </tr>
                <tr>
                    <td width="200">Simplyhired.com</td>
                    <td><?php echo $checkboxSimplyhired ?></td>
                </tr>

                <tr>
                    <td></td>
                    <td bgcolor="white">
                        <a href="http://www.simplyhired.com" target="_new">
                            <img
                                    src="<?php echo bloginfo('url') ?>/wp-content/plugins/wp-eazymatch/assets/img/simplyhired-logo-orange.png"
                                    style="width:100px;border: solid 1px #ccc;"/>
                        </a>
                        <br/>
                        <a href="<?php echo bloginfo('url') ?>/em-jobfeed/simplyhired"
                           target="_new"><?php echo bloginfo('url') ?>/em-jobfeed/simplyhired</a><br/>
                        specificaties: <a href="http://www.simplyhired.com/a/add-jobs/example-xml" target="_blank">http://www.simplyhired.com/a/add-jobs/example-xml</a>
                    </td>
                </tr>
                <tr>
                    <td width="200">Adzuno.com</td>
                    <td><?php echo $checkboxadzuna ?></td>
                </tr>

                <tr>
                    <td></td>
                    <td bgcolor="white">
                        <a href="https://www.adzuna.com" target="_new">
                            <img
                                    src="<?php echo bloginfo('url') ?>/wp-content/plugins/wp-eazymatch/assets/img/logo-adzuna.png"
                                    style="width:100px;border: solid 1px #ccc;"/>
                        </a>
                        <br/>
                        <a href="<?php echo bloginfo('url') ?>/em-jobfeed/adzuna"
                           target="_new"><?php echo bloginfo('url') ?>/em-jobfeed/adzuna</a><br/>
                    </td>
                </tr>
                <tr>
                    <td width="200">Trovit</td>
                    <td><?php echo $checkboxTrovit ?></td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td bgcolor="white">
                        <a href="http://vacatures.trovit.nl/" target="_new">
                            <img
                                    src="<?php echo bloginfo('url') ?>/wp-content/plugins/wp-eazymatch/assets/img/trovit-logo.png"
                                    style="width:100px;"/>
                        </a>
                        <br/>
                        <a href="<?php echo bloginfo('url') ?>/em-jobfeed/trovit"
                           target="_new"><?php echo bloginfo('url') ?>/em-jobfeed/trovit</a><br/>
                        specificaties: <a
                                href="http://about.trovit.com/validator/">http://about.trovit.com/validator/</a>
                    </td>
                </tr>
                <tr>
                    <td width="200">Sitemap (XML)</td>
                    <td><?php echo $checkboxSitemap ?></td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td bgcolor="white">
                        <a href="http://www.sitemaps.org/protocol.php" target="_new">
                            Sitemaps.org
                        </a>
                        <br/>
                        <a href="<?php echo bloginfo('url') ?>/em-jobfeed/sitemap"
                           target="_new"><?php echo bloginfo('url') ?>/em-jobfeed/sitemap</a>
                    </td>
                </tr>
                <tr>
                    <td width="200">JSON</td>
                    <td><?php echo $checkboxJson ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td bgcolor="white">
                        &nbsp;
                        <br/>
                        <a href="<?php echo bloginfo('url') ?>/em-jobfeed/json"
                           target="_new"><?php echo bloginfo('url') ?>/em-jobfeed/json</a>
                    </td>
                </tr>
            </table>
            <br/ >
            <table class="emol-welcome-panel" style="width: 100%;">
                <tr>
                    <td colspan="2" class="cTdh"><br>

                        <h2>EazyMatch Social media</h2></td>
                </tr>
                <tr>
                    <td width="200">&nbsp;</td>
                    <td>
                        <a href="http://www.facebook.com/help/?page=818" target="_new">
                            <img
                                    src="<?php echo bloginfo('url') ?>/wp-content/plugins/wp-eazymatch/assets/img/facebook-logo.png"
                                    style="width:100px;"/>
                        </a> &nbsp;
                        <a href="http://blog.linkedin.com/2008/11/14/share-and-discuss-news-within-linkedin-groups/"
                           target="_new">
                            <img
                                    src="<?php echo bloginfo('url') ?>/wp-content/plugins/wp-eazymatch/assets/img/linkedin-logo.png"
                                    style="width:100px;"/>
                        </a>&nbsp;
                        <a href="http://www.twitterfeed.com" target="_new">
                            <img
                                    src="<?php echo bloginfo('url') ?>/wp-content/plugins/wp-eazymatch/assets/img/twitter-logo.png"
                                    style="width:100px;border: solid 1px #ccc;"/>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="200">ATOM</td>
                    <td><?php echo $checkboxAtom ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td bgcolor="white">
                        <a href="<?php echo bloginfo('url') ?>/em-jobfeed/atom"
                           target="_new"><?php echo bloginfo('url') ?>/em-jobfeed/atom</a>
                    </td>
                </tr>
                <tr>
                    <td width="200">RSS</td>
                    <td><?php echo $checkboxRss ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td bgcolor="white">
                        <a href="<?php echo bloginfo('url') ?>/em-jobfeed/rss"
                           target="_new"><?php echo bloginfo('url') ?>/em-jobfeed/rss</a>
                    </td>
                </tr>
                <tr>
                    <td width="200">RSS (full)</td>
                    <td><?php echo $checkboxRssfull ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td bgcolor="white">
                        <a href="<?php echo bloginfo('url') ?>/em-jobfeed/rssfull"
                           target="_new"><?php echo bloginfo('url') ?>/em-jobfeed/rssfull</a>
                    </td>
                </tr>
            </table>
        </div>
        <hr/>
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Opslaan') ?>"/>
        </p>
    </form>
	<?php
}