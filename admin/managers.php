<?php
function eazymatch_plugin_manager() {

	if ( ! get_option( 'emol_apihash' ) ) {
		wp_die( __( 'No eazymatch connection active.' ) );

	}

	//must check that the user has the required capability
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	// variables for the field and option names
	$hidden_field_name = 'mt_submit_hidden';

	/**
	 *
	 */
	$eazymatchOptions = array(
		'emol_manager_settings'     => get_option( 'emol_manager_settings' ),
		'emol_view_manager_heading' => get_option( 'emol_view_manager_heading' ),
		'emol_view_manager_contact' => get_option( 'emol_view_manager_contact' ),
	);


	if ( isset( $_GET['checkforupdates'] ) ) {
		global $MyUpdateChecker;
		$MyUpdateChecker->checkForUpdates();
	};

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if ( isset( $_POST[ $hidden_field_name ] ) && $_POST[ $hidden_field_name ] == 'Y' ) {

		foreach ( $_POST as $option => $value ) {
			if ( ! get_option( $option ) ) {
				add_option( $option );
			}
			if ( $option == 'emol_manager_settings' ) {
				$value = serialize( $value );
			}
			update_option( $option, ( $value ) );

			$eazymatchOptions[ $option ] = ( $value );
		}
		//always reset the session hash


		?>
        <div class="updated"><p><strong><?php _e( EMOL_ADMIN_SAVEMSG, 'Emol-3.0-identifier' ); ?></strong></p></div>
		<?php
	}

	$wordpressManagerSettings = array();
	if ( ! empty( $eazymatchOptions['emol_manager_settings'] ) ) {
		$wordpressManagerSettings = unserialize( $eazymatchOptions['emol_manager_settings'] );
	}

	echo '<div class="wrap">';
	echo "<h2>" . __( 'EazyMatch > ' . EMOL_ADMIN_SETTINGS . ' > ' . EMOL_ADMIN_MANAGER . ' ', 'Emol-3.0-identifier' ) . "</h2>";

	// settings form


	//first connect to the api
	$emolApi = eazymatch_connect();


	if ( ! $emolApi ) {
		//   eazymatch_trow_error();
	}

	//try {
	$userService = $emolApi->get( 'licence' );
	$users       = $userService->allUsers();
	//} catch(Exception $e){
	//    $users = array();
	//}

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
    <form name="form1" method="post">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

        <div align="right"><a href="http://www.eazymatch.nl" target="_new"><img
                        src="http://www.eazymatch.nl/wordpress_logo.png"/></a></div>
        <div id="emol-admin-table">
			<?php //emol_dump($wordpressManagerSettings)
			?>
            <form method="post">
                <div class="emol-admin-table">
                    <table class="emol-welcome-panel" style="width: 100%;">
                        <tr>
                            <td width="250"><label>Contactblok weergeven (vacature)?</label></td>
                            <td><select name="emol_view_manager_contact">
                                    <option
                                            value="0" <?php if ( $eazymatchOptions['emol_view_manager_contact'] == 0 ) {
										echo 'selected="selected"';
									} ?>>
                                        Nee
                                    </option>
                                    <option
                                            value="1" <?php if ( $eazymatchOptions['emol_view_manager_contact'] == 1 ) {
										echo 'selected="selected"';
									} ?>>
                                        Ja
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Naam / titel van het contactblok</label></td>
                            <td><input name="emol_view_manager_heading" type="text"
                                       style="width:100%;font-size:110%;font-weight: bold;"
                                       value="<?php echo @$eazymatchOptions['emol_view_manager_heading'] ?>"/>
                            </td>
                        </tr>
                    </table>
                </div>
                <hr>
				<?php if ( ! empty( $users ) ) {
					foreach ( $users as $user ) {
						if ( $user['active'] == false ) {
							continue;
						}
						?>
                        <input type="hidden" name="emol_manager_settings[<?php echo $user['id'] ?>][id]"
                               value="<?php echo $user['id'] ?>">
                        <div class="emol-admin-table">
                            <table class="emol-welcome-panel" style="width: 100%;">
                                <tr>
                                    <td width="250">User</td>
                                    <td width="350"><strong><?php echo $user['id'] ?>
                                            , <?php echo $user['fullname'] ?></strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td width="250">Weergave naam</td>
                                    <td colspan="2"><input type="text" style="width: 100%;"
                                                           name="emol_manager_settings[<?php echo $user['id'] ?>][displayname]"
                                                           value="<?php echo ( ! empty( $wordpressManagerSettings[ $user['id'] ]['displayname'] ) ) ? $wordpressManagerSettings[ $user['id'] ]['displayname'] : $user['fullname'] ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="250">Email vermelden</td>
                                    <td colspan="2"><input type="text" style="width: 100%;"
                                                           name="emol_manager_settings[<?php echo $user['id'] ?>][email]"
                                                           value="<?php echo ( ! empty( $wordpressManagerSettings[ $user['id'] ]['email'] ) ) ? $wordpressManagerSettings[ $user['id'] ]['email'] : '' ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="250">Telefoon vermelden</td>
                                    <td colspan="2"><input type="text" style="width: 100%;"
                                                           name="emol_manager_settings[<?php echo $user['id'] ?>][phone]"
                                                           value="<?php echo ( ! empty( $wordpressManagerSettings[ $user['id'] ]['phone'] ) ) ? $wordpressManagerSettings[ $user['id'] ]['phone'] : '' ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="250">Foto (url/medialibrary)</td>
                                    <td colspan="2"><input type="text" style="width: 100%;"
                                                           name="emol_manager_settings[<?php echo $user['id'] ?>][photo]"
                                                           value="<?php echo ( ! empty( $wordpressManagerSettings[ $user['id'] ]['photo'] ) ) ? $wordpressManagerSettings[ $user['id'] ]['photo'] : '' ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="250">Contacttekst</td>
                                    <td colspan="2"><textarea style="width:100%;height:100px;"
                                                              name="emol_manager_settings[<?php echo $user['id'] ?>][text]"><?php
											echo ( ! empty( $wordpressManagerSettings[ $user['id'] ]['text'] ) ) ? $wordpressManagerSettings[ $user['id'] ]['text'] : '' ?></textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <hr/>
						<?php
					}
				} ?>
                <p class="submit">
                    <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Opslaan' ) ?>"/>
                </p>
            </form>
        </div>
        <hr/>


    </form>


	<?php


}