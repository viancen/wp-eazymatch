<?php
function eazymatch_plugin_cv() {

	if ( ! get_option( 'emol_apihash' ) ) {
		wp_die( __( 'No eazymatch connection active.' ) );

	}
	//must check that the user has the required capability
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	if ( ! get_option( 'emol_instance' ) ) {
		echo "
            <div style=\"background-color:#ffebeb;border:1px solid red;margin-top:10px;text-align:center;font-weight:bold;padding:15px;\">
            FIRST SETUP YOUR CREDENTIALS UNDER GLOBAL
            </div>";
	} else {


		//we actually use eazymatch here too...
		$api       = eazymatch_connect();
		$appStatus = null;
		try {
			$wsApp     = $api->get( 'tree' );
			$appStatus = $wsApp->tree( 'Applicantstatus' );
		} catch ( Exception $e ) {
			echo "<div style=\"background-color:#ffebeb;border:1px solid red;margin-top:10px;text-align:center;font-weight:bold;padding:15px;\">API ERROR.</div>";
			exit();
		}
		//$appStatus
		if ( is_null( $appStatus ) ) {
			echo "<div style=\"background-color:#ffebeb;border:1px solid red;margin-top:10px;text-align:center;font-weight:bold;padding:15px;\">ERROR IN CONNECTION, NULL RESPONSE</div>";
			exit();
		}

		// variables for the field and option names
		$hidden_field_name = 'mt_submit_hidden';

		$eazymatchOptions = array(
			'emol_cv_header'             => get_option( 'emol_cv_header' ),
			'emol_cv_secure'             => get_option( 'emol_cv_secure' ),
			'emol_cv_url'                => get_option( 'emol_cv_url' ),
			'emol_cv_amount_pp'          => get_option( 'emol_cv_amount_pp' ),
			'emol_cv_search_url'         => get_option( 'emol_cv_search_url' ),
			'emol_cv_search_picture'     => get_option( 'emol_cv_search_picture' ),
			'emol_react_cv_error_secure' => get_option( 'emol_react_cv_error_secure' ),
			'emol_cv_search_desc'        => get_option( 'emol_cv_search_desc' ),
			'emol_react_url_cv'          => get_option( 'emol_react_url_cv' ),
			'emol_cv_no_result'          => get_option( 'emol_cv_no_result' ),
			'emol_filter_app_options'    => get_option( 'emol_filter_app_options' ),
			'emol_react_success'         => get_option( 'emol_react_success' )
		);

		//check the filter options, it will generate an error if not existing
		if ( ! get_option( 'emol_filter_app_options' ) ) {
			add_option( 'emol_filter_app_options' );
			update_option( 'emol_filter_app_options', serialize( array() ) );
		}

		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'
		if ( isset( $_POST[ $hidden_field_name ] ) && $_POST[ $hidden_field_name ] == 'Y' ) {

			if ( ! is_numeric( $_POST['emol_cv_amount_pp'] ) ) {
				$_POST['emol_cv_amount_pp'] = 5;
			}

			//first our arrays
			if ( isset( $_POST['filter'] ) ) {
				$types = $_POST['filter'];
				unset( $_POST['filter'] );
				if ( ! empty( $types ) ) {
					$_POST['emol_filter_app_options'] = serialize( $types );
				} else {
					$_POST['emol_filter_app_options'] = serialize( array() );
				}
			} else {
				$_POST['emol_filter_app_options'] = serialize( array() );
			}

			foreach ( $_POST as $option => $value ) {

				if ( ! get_option( $option ) ) {
					add_option( $option );
				}
				update_option( $option, $value );

				$eazymatchOptions[ $option ] = $value;
			}


			?>
            <div class="updated"><p><strong><?php _e( EMOL_ADMIN_SAVEMSG, 'Emol-3.0-identifier' ); ?></strong></p></div>
			<?php
		}


		// $filterOptions = get_option('emol_filter_options');
		$filterOptions = unserialize( $eazymatchOptions['emol_filter_app_options'] );// Wordpress does not do this for us
		if ( ! $filterOptions ) {
			$filterOptions = array();
		}
		//create filterlist
		if ( is_array( $appStatus ) && count( $appStatus ) > 0 ) {
			$list = '<ul id="emol-admin-filter-list">';
			foreach ( $appStatus as $status ) {
				foreach ( $status['children'] as $firstChildren ) {
					$checked = '';

					if ( in_array( $firstChildren['id'], $filterOptions ) ) {
						$checked = 'checked="checked"';
					}
					$list .= "<li><input type=\"checkbox\" name=\"filter[]\" " . $checked . " value=\"" . $firstChildren['id'] . "\"> " . $firstChildren['name'] . '';
					if ( isset( $firstChildren['children'] ) && count( $firstChildren['children'] ) > 0 ) {
						$list .= '<ul>';
						foreach ( $firstChildren['children'] as $secondChildren ) {
							$checked2 = '';
							if ( in_array( $secondChildren['id'], $filterOptions ) ) {
								$checked2 = 'checked="checked"';
							}
							$list .= "<li><input type=\"checkbox\" name=\"filter[]\" " . $checked2 . " value=\"" . $secondChildren['id'] . "\"> " . $secondChildren['name'] . '</li>';
						}
						$list .= '</ul>';
					}
					$list .= '</li>';
				}
			}
			$list .= '</ul>';
		} else {
			$list = '--';
		}

		//checkbox for picture on/off
		$sel2 = 'checked="checked"';
		$sel1 = '';
		if ( get_option( 'emol_cv_search_picture' ) == 1 ) {
			$sel1 = 'checked="checked"';
			$sel2 = '';
		}
		$checkboxPicture = '<input type="radio" name="emol_cv_search_picture" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
		$checkboxPicture .= '<input type="radio" name="emol_cv_search_picture" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . ' &nbsp;';


		//description
		$sel2 = 'checked="checked"';
		$sel1 = '';
		if ( get_option( 'emol_cv_search_desc' ) == 1 ) {
			$sel1 = 'checked="checked"';
			$sel2 = '';
		}
		$checkboxDescr = '<input type="radio" name="emol_cv_search_desc" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
		$checkboxDescr .= '<input type="radio" name="emol_cv_search_desc" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

		//emol_cv_secure
		$sel2 = 'checked="checked"';
		$sel1 = '';
		if ( get_option( 'emol_cv_secure' ) == 1 ) {
			$sel1 = 'checked="checked"';
			$sel2 = '';
		}
		$checkboxemol_cv_secure = '<input type="radio" name="emol_cv_secure" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
		$checkboxemol_cv_secure .= '<input type="radio" name="emol_cv_secure" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';


		echo '<div class="wrap">';
		echo "<h2>" . __( 'EazyMatch > ' . EMOL_ADMIN_SETTINGS . ' > ' . EMOL_ADMIN_CV . ' ', 'Emol-3.0-identifier' ) . "</h2>";
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
        <strong><?php echo get_option( 'emol_instance' ) ?></strong><br/>
        Shorttag: <strong>[eazymatch view="cv"]</strong>
        <form name="form1" method="post" action="">
            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

            <div id="emol-admin-table">
                <table class="welcome-panel" style="width: 100%;">
                    <tr>
                        <td colspan="3" class="cTdh"><br>

                            <h2><?php echo EMOL_ADMIN_SETTINGS . ' - ' . EMOL_ADMIN_CV ?></h2></td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_CVDISPLAY_URL, 'Emol-3.0-identifier' ); ?> </td>
                        <td><input type="text" name="emol_cv_url"
                                   value="<?php echo $eazymatchOptions['emol_cv_url']; ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_CVSEARCH_URL, 'Emol-3.0-identifier' ); ?> </td>
                        <td><input type="text" name="emol_cv_search_url"
                                   value="<?php echo $eazymatchOptions['emol_cv_search_url']; ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_CV_SECURE, 'Emol-3.0-identifier' ); ?> </td>
                        <td><?php echo $checkboxemol_cv_secure; ?></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_CV_PICTURE, 'Emol-3.0-identifier' ); ?> </td>
                        <td><?php echo $checkboxPicture; ?></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_CV_DESC, 'Emol-3.0-identifier' ); ?> </td>
                        <td><?php echo $checkboxDescr; ?></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_RESULTSPERPAGE, 'Emol-3.0-identifier' ); ?> </td>
                        <td><input type="text" name="emol_cv_amount_pp"
                                   value="<?php echo $eazymatchOptions['emol_cv_amount_pp']; ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_CV_HEADER, 'Emol-3.0-identifier' ); ?> </td>
                        <td><input type="text" name="emol_cv_header"
                                   value="<?php echo $eazymatchOptions['emol_cv_header']; ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_CV_ERROR_SECURE, 'Emol-3.0-identifier' ); ?> </td>
                        <td><input type="text" name="emol_react_cv_error_secure"
                                   value="<?php echo $eazymatchOptions['emol_react_cv_error_secure']; ?>" size="40">
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="cTdh"><br>

                            <h2><?php echo EMOL_ADMIN_REACTING ?></h2></td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_REACT_URL, 'Emol-3.0-identifier' ); ?> </td>
                        <td><input type="text" name="emol_react_url_cv"
                                   value="<?php echo $eazymatchOptions['emol_react_url_cv']; ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><?php _e( EMOL_ADMIN_MSGNORESULT, 'Emol-3.0-identifier' ); ?> </td>
                        <td><textarea name="emol_cv_no_result" cols="62"
                                      rows="7"><?php echo stripslashes( $eazymatchOptions['emol_cv_no_result'] ); ?></textarea>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><?php _e( EMOL_ADMIN_MSGAFTERREACT, 'Emol-3.0-identifier' ); ?> </td>
                        <td><textarea name="emol_react_success" cols="62"
                                      rows="7"><?php echo stripslashes( $eazymatchOptions['emol_react_success'] ); ?></textarea>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="cTdh"><br>

                            <h2><?php echo EMOL_ADMIN_FILTERS ?></h2></td>
                    </tr>
                    <tr>
                        <td valign="top">Filters</td>
                        <td colspan="2">
							<?php
							echo $list;
							?>
                        </td>
                    </tr>
                </table>
            </div>

            <p class="submit">
                <input type="submit" name="Submit" class="button-primary"
                       value="<?php esc_attr_e( EMOL_ACCOUNT_SAVE ) ?>"/>
            </p>

        </form>
		<?php
	}
}
