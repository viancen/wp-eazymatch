<?php
function eazymatch_plugin_stylesheet() {

	if ( ! get_option( 'emol_apihash' ) ) {
		wp_die( __( 'No eazymatch connection active.' ) );

	}
	//must check that the user has the required capability
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	// variables for the field and option names
	$hidden_field_name = 'mt_submit_hidden';


	$eazymatchOptions = array(
		'emol_strip_html' => get_option( 'emol_strip_html' ),
		'emol_stylesheet' => get_option( 'emol_stylesheet' )
	);

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if ( isset( $_POST[ $hidden_field_name ] ) && $_POST[ $hidden_field_name ] == 'Y' ) {

		foreach ( $_POST as $option => $value ) {
			if ( ! get_option( $option ) ) {
				add_option( $option );
			}
			if ( $option == 'emol_stylesheet' ) {
				update_option( $option, $value );

				$uploadinfo = wp_upload_dir();
				file_put_contents( $uploadinfo['basedir'] . '/eazymatch.style.css', stripcslashes( $value ) );
			} else {
				update_option( $option, $value );
			}
			$eazymatchOptions[ $option ] = $value;
		}
		//always reset the session hash


		?>
        <div class="updated"><p><strong><?php _e( EMOL_ADMIN_SAVED, 'Emol-3.0-identifier' ); ?></strong></p></div>
		<?php
	}

	echo '<div class="wrap">';
	echo "<h2>" . __( 'EazyMatch > ' . EMOL_ADMIN_SETTINGS . ' > ' . EMOL_ADMIN_STYLESHEET . ' ', 'Emol-3.0-identifier' ) . "</h2>";

	// settings form
	$sel2 = 'checked="checked"';
	$sel1 = '';
	if ( get_option( 'emol_strip_html' ) == 1 ) {
		$sel1 = 'checked="checked"';
		$sel2 = '';
	}
	$checkboxHTML = '<input type="radio" name="emol_strip_html" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
	$checkboxHTML .= '<input type="radio" name="emol_strip_html" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

	?>
    <style type="">

        #emol-admin-table tr td {
            background-color: #f6f6f6;
            padding: 5px;
        }

        #emol-admin-table .cTdh {
            background-color: #f9f9f9;
            padding: 5px;
        }

        #emol_stylesheet {
            font-family: 'courier new';
            font-size: 12px;
            line-height: 1.6em;
        }

    </style>
    <form name="form1" method="post" action="">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

        <div id="emol-admin-table">
            <table cellpadding="4" width="100%">
                <tr>
                    <td colspan="2" class="cTdh"><br>

                        <h2>EazyMatch Stylesheet</h2></td>
                </tr>
                <tr>
                    <td width="200">Strip html (jobs / cv)</td>
                    <td><?php echo $checkboxHTML ?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <textarea name="emol_stylesheet" id="emol_stylesheet" style="width:100%;height:500px;"
                                  class="tab_text"><?php echo stripcslashes( $eazymatchOptions['emol_stylesheet'] ); ?></textarea>
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