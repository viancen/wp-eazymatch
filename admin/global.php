<?php
function eazymatch_plugin_options() {


	//must check that the user has the required capability
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	if ( isset( $_GET['do_connect'] ) ) {
		$data_connection = eazymatch_connect();

	}

	// variables for the field and option names
	$hidden_field_name = 'mt_submit_hidden';

	if ( ! get_option( 'emol_service_url' ) ) {
		add_option( 'emol_service_url', 'https://api.eazymatch.cloud' );
	}
	/**
	 * ALSO ADJUST EMOL-INSTALL WHEN EDITTING THIS!!
	 *
	 */
	$eazymatchOptions = array(

		'emol_service_url'         => get_option( 'emol_service_url' ),
		'emol_instance'            => get_option( 'emol_instance' ),
		'emol_lang'                => get_option( 'emol_lang' ),
		'emol_key'                 => get_option( 'emol_key' ),
		'emol_secret'              => get_option( 'emol_secret' ),
		'emol_url'                 => get_option( 'emol_url' ),
		'emol_account_url'         => get_option( 'emol_account_url' ),
		'emol_logout_url'          => get_option( 'emol_logout_url', '/' ),
		'emol_jquery_ui_skin'      => get_option( 'emol_jquery_ui_skin' ),
		'emol_company_account_url' => get_option( 'emol_company_account_url' ),
		'emol_base_address' => get_option( 'emol_base_address' ),
		'emol_base_city' => get_option( 'emol_base_city' ),
		'emol_base_zipcode' => get_option( 'emol_base_zipcode' ),
		'emol_base_region' => get_option( 'emol_base_region' ),
		'emol_base_country' => get_option( 'emol_base_country' ),
	);


	if ( isset( $_GET['refreshconnection'] ) && $_GET['refreshconnection'] ) {
		delete_option( 'emol_apihash' );
	}

	//for the update test
	//base does not exist anymore
	$current_jquery_ui = get_option( 'emol_jquery_ui_skin' );
	if ( $current_jquery_ui == 'base' ) {
		update_option( 'emol_jquery_ui_skin', 'smoothness' );
		$eazymatchOptions['emol_jquery_ui_skin'] = 'smoothness';
	}

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if ( isset( $_POST[ $hidden_field_name ] ) && $_POST[ $hidden_field_name ] == 'Y' ) {

		foreach ( $_POST as $option => $value ) {
			if ( ! get_option( $option ) ) {
				add_option( $option );
			}
			update_option( $option, $value );

			$eazymatchOptions[ $option ] = $value;
		}
		//always reset the session hash

		?>
        <div class="updated"><p><strong><?php _e( EMOL_ADMIN_SAVEMSG, 'Emol-3.0-identifier' ); ?></strong></p></div>
		<?php
	}

	echo '<div class="wrap">';
	echo "<h2>" . __( 'EazyMatch > ' . EMOL_ADMIN_SETTINGS . ' > ' . EMOL_ADMIN_LICENCE . ' ', 'Emol-3.0-identifier' ) . "</h2>";

	// settings form

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

        <div align="right"><a href="https://www.eazymatch-online.nl" target="_new"><img
                        src="https://base.eazymatch.cloud/images/login-img/EazyMatch-vector.svg"/></a></div>
        <div id="emol-admin-table">
            <table class="welcome-panel" style="width: 100%;">
                <tr>
                    <td colspan="3" class="cTdh"><br>
                        <h2>Uw EazyMatch account</h2> Versie plugin :<strong>  <?php echo EMOL_VERSION ?></strong></td>
                </tr>
                <tr>
                    <td width="200">&nbsp;</td>
                    <td><?php
						$current_connection = get_option( 'emol_apihash' );
						if ( ! empty( $current_connection ) ) {
							?>
                            <button type="button"
                                    onclick="window.location = '/wp-admin/admin.php?page=emol-admin&refreshconnection=true'">
                                Connectie met EazyMatch vernieuwen
                            </button>
							<?php
						} ?>

                        <br>


                        <button type="button"
                                onclick="window.location = '/wp-admin/admin.php?page=emol-admin&do_connect=true'">
                            Connect met eazymatch
                        </button>
                    </td>
                    <td></td>
                </tr>

                <tr>
                    <td width="200"><?php _e( "API:", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_service_url"
                               value="<?php echo $eazymatchOptions['emol_service_url']; ?>" size="40"></td>
                    <td></td>
                </tr>
                <tr>
                    <td width="200"><?php _e( "Instance naam:", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_instance"
                               value="<?php echo $eazymatchOptions['emol_instance']; ?>" size="40"></td>
                    <td></td>
                </tr>
                <tr>
                    <td><?php _e( "Key:", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_key" value="<?php echo $eazymatchOptions['emol_key']; ?>"
                               size="40"></td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( "Secret:", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_secret" value="<?php echo $eazymatchOptions['emol_secret']; ?>"
                               size="40"></td>
                    <td>
                    </td>
                </tr>

                <tr>
                    <td><?php _e( "CustomerAddress:", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_base_address" value="<?php echo @$eazymatchOptions['emol_base_address']; ?>"
                               size="40"></td>
                    <td>
                    </td>
                </tr>

                <tr>
                    <td><?php _e( "CustomerCity:", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_base_city" value="<?php echo @$eazymatchOptions['emol_base_city']; ?>"
                               size="40"></td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( "CustomerRegion:", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_base_region" value="<?php echo @$eazymatchOptions['emol_base_region']; ?>"
                               size="40"></td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( "CustomerZipcode:", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_base_zipcode" value="<?php echo @$eazymatchOptions['emol_base_zipcode']; ?>"
                               size="40"></td>
                    <td>
                    </td>
                </tr>

                <tr>
                    <td><?php _e( "CustomerCountry:", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_base_country" value="<?php echo @$eazymatchOptions['emol_base_country']; ?>"
                               size="40"></td>
                    <td>
                    </td>
                </tr>

                <tr>
                    <td><?php _e( "Lang:", 'Emol-3.0-identifier' ); ?> </td>
                    <td>
                        <select name="emol_lang" style="width: 150px;">
                            <option <?php if ( (string) $eazymatchOptions['emol_lang'] == 'nl-NL' ) {
								echo 'selected="selected"';
							} ?> value='nl-NL'><?php echo EMOL_NL ?></option>
                            <option <?php if ( (string) $eazymatchOptions['emol_lang'] == 'en-EN' ) {
								echo 'selected="selected"';
							} ?> value='en-EN'><?php echo EMOL_EN ?></option>
                        </select>
                    </td>
                    <td>
                    </td>
                </tr>

                <tr>
                    <td colspan="3" class="cTdh"><br>

                        <h2>Accounts</h2></td>
                </tr>
                <tr>
                    <td><?php _e( "Account url kandidaat: ", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_account_url"
                               value="<?php echo $eazymatchOptions['emol_account_url']; ?>" size="40"></td>
                    <td>Bijvoorbeeld: <b>account</b>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( "Account uitgelogd url kandidaat: ", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_logout_url"
                               value="<?php echo $eazymatchOptions['emol_logout_url']; ?>" size="40"></td>
                    <td>Bijvoorbeeld: <b>uitgelogd</b>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( "Account url bedrijf: ", 'Emol-3.0-identifier' ); ?> </td>
                    <td><input type="text" name="emol_company_account_url"
                               value="<?php echo $eazymatchOptions['emol_company_account_url']; ?>" size="40"></td>
                    <td>Bijvoorbeeld: <b>account-werkgevers</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="3" class="cTdh"><br>

                        <h2>JQuery</h2></td>
                </tr>
                <tr>
                    <td><?php _e( "Jquery UI insluiten?: ", 'Emol-3.0-identifier' ); ?> </td>
                    <td><?php
						$listThemes = scandir( dirname( dirname( __FILE__ ) ) . '/assets/jquery-ui/themes/' );

						?>
                        <select name="emol_jquery_ui_skin">
                            <option value="">No Jquery UI (Jquery-UI is already included by theme or other plugin)
                            </option>
							<?php

							foreach ( $listThemes as $oui ) {
								if ( ! in_array( $oui, array( '.', '..', 'index.html' ) ) ) {
									?>
                                    <option value="<?php echo $oui ?>" <?php if ( $eazymatchOptions['emol_jquery_ui_skin'] == $oui )
										echo 'selected="selected"' ?>><?php echo $oui ?></option>
									<?php
								}
							}
							?>
                        </select>
                    </td>
                    <td>
                        <a href="https://jqueryui.com/themeroller/" target="_new">http://code.jquery.com/ui/</a>

                    </td>
                </tr>

            </table>
        </div>
        <hr/>

        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Opslaan' ) ?>"/>
        </p>

    </form>


	<?php


}