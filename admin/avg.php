<?php


function eazymatch_plugin_avg() {
	//must check that the user has the required capability
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	if ( ! get_option( 'emol_instance' ) ) {
		echo "<div style=\"background-color:#ffebeb;border:1px solid red;margin-top:10px;text-align:center;font-weight:bold;padding:15px;\">FIRST SETUP YOUR CREDENTIALS UNDER GLOBAL</div>";
	} else {
		// variables for the field and option names
		$hidden_field_name = 'mt_submit_hidden';

		$eazymatchOptions = array(
			'emol_frm_avg'       => get_option( 'emol_frm_avg' ),
			'emol_frm_avg_name'  => get_option( 'emol_frm_avg_name' ),
			'emol_frm_avg_phone' => get_option( 'emol_frm_avg_phone' ),
			'emol_frm_avg_email' => get_option( 'emol_frm_avg_email' ),
		);


		$_POST    = array_map( 'stripslashes_deep', $_POST );
		$_GET     = array_map( 'stripslashes_deep', $_GET );
		$_COOKIE  = array_map( 'stripslashes_deep', $_COOKIE );
		$_REQUEST = array_map( 'stripslashes_deep', $_REQUEST );

		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'
		if ( isset( $_POST[ $hidden_field_name ] ) && $_POST[ $hidden_field_name ] == 'Y' ) {

			foreach ( $_POST as $option => $value ) {

				// prevent editing options freely
				if ( ! array_key_exists( $option, $eazymatchOptions ) ) {
					continue;
				}

				update_option( $option, $value );
				$eazymatchOptions[ $option ] = $value;
			}

			?>
            <div class="updated"><p><strong><?php _e( EMOL_ADMIN_SAVED, 'Emol-3.0-identifier' ); ?></strong></p></div>
			<?php
		}

		echo '<div class="wrap">';
		echo "<h2>" . __( 'EazyMatch > ' . EMOL_ADMIN_SETTINGS . ' > ' . EMOL_ADMIN_FORM . ' ', 'Emol-3.0-identifier' ) . "</h2>";

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
        <strong><?php echo get_option( 'emol_instance' ) ?></strong>
        <form name="form1" method="post" action="">
            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y"/>

            <div id="emol-admin-table">
                <br/>

                <h2><?php echo EMOL_ADMIN_SETTINGS . ' - ' . EMOL_ADMIN_FORM_AVG ?></h2>

                <table class="welcome-panel" style="width: 100%;">
                    <colgroup>
                        <col style="width: 200px;"/>
                        <col style="width: 350px;"/>
                    </colgroup>
                    <tr>
                        <td><?php _e( EMOL_AVG_NAME, 'Emol-3.0-identifier' ); ?></td>
                        <td><input type="text" name="emol_frm_avg_name"
                                   value="<?php echo $eazymatchOptions['emol_frm_avg_name']; ?>"></td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_AVG_EMAIL, 'Emol-3.0-identifier' ); ?></td>
                        <td><input type="text" name="emol_frm_avg_email"
                                   value="<?php echo $eazymatchOptions['emol_frm_avg_email']; ?>"></td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_AVG_PHONE, 'Emol-3.0-identifier' ); ?></td>
                        <td><input type="text" name="emol_frm_avg_phone"
                                   value="<?php echo $eazymatchOptions['emol_frm_avg_phone']; ?>"></td>
                    </tr>
                    <tr>
                        <td valign="top"><?php _e( EMOL_AVG_TITLE, 'Emol-3.0-identifier' ); ?><br/></td>
                        <td>
							<?php
							$settings = array(

								'textarea_rows' => 40,
								'tabindex'      => 1
							);
							wp_editor( $eazymatchOptions['emol_frm_avg'] ? $eazymatchOptions['emol_frm_avg'] : EMOL_AVG, 'emol_frm_avg', $settings );
							?>
                        </td>
                    </tr>
                </table>

            </div>
            <hr/>

            <p class="submit">
                <input type="submit" name="Submit" class="button-primary"
                       value="<?php esc_attr_e( EMOL_ACCOUNT_SAVE ) ?>"/>
            </p>

        </form>
		<?php
	}
}