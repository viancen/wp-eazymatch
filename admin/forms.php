<?php
function eazymatch_plugin_form() {
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
			'emol_frm_app_name'             => get_option( 'emol_frm_app_name' ),
			'emol_frm_app_firstname'        => get_option( 'emol_frm_app_firstname' ),
			'emol_frm_app_middlename'       => get_option( 'emol_frm_app_middlename' ),
			'emol_frm_app_lastname'         => get_option( 'emol_frm_app_lastname' ),
			'emol_frm_app_address'          => get_option( 'emol_frm_app_address' ),
			'emol_frm_app_street'           => get_option( 'emol_frm_app_street' ),
			'emol_frm_app_housenr'          => get_option( 'emol_frm_app_housenr' ),
			'emol_frm_app_zipcode'          => get_option( 'emol_frm_app_zipcode' ),
			'emol_frm_app_city'             => get_option( 'emol_frm_app_city' ),
			'emol_frm_app_nationality'      => get_option( 'emol_frm_app_nationality' ),
			'emol_frm_app_country'          => get_option( 'emol_frm_app_country' ),
			'emol_frm_app_maritalstatus'    => get_option( 'emol_frm_app_maritalstatus' ),
			'emol_frm_app_extension'        => get_option( 'emol_frm_app_extension' ),
			'emol_frm_app_contactvia'       => get_option( 'emol_frm_app_contactvia' ),
			'emol_frm_app_birthdate'        => get_option( 'emol_frm_app_birthdate' ),
			'emol_frm_app_ssn'              => get_option( 'emol_frm_app_ssn' ),
			'emol_frm_app_cv'               => get_option( 'emol_frm_app_cv' ),
			'emol_frm_app_photo'            => get_option( 'emol_frm_app_photo' ),
			'emol_frm_app_email'            => get_option( 'emol_frm_app_email' ),
			'emol_frm_app_phone'            => get_option( 'emol_frm_app_phone' ),
			'emol_frm_app_phone2'           => get_option( 'emol_frm_app_phone2' ),
			'emol_frm_app_title'            => get_option( 'emol_frm_app_title' ),
			'emol_frm_app_managercompany'   => get_option( 'emol_frm_app_managercompany' ),
			'emol_frm_app_password'         => get_option( 'emol_frm_app_password' ),
			'emol_frm_app_schoolingtype_id' => get_option( 'emol_frm_app_schoolingtype_id' ),
			'emol_frm_app_availablehours'   => get_option( 'emol_frm_app_availablehours' ),
			'emol_frm_app_salary'           => get_option( 'emol_frm_app_salary' ),
			'emol_frm_app_searchlocation'   => get_option( 'emol_frm_app_searchlocation' ),
			'emol_frm_app_motivation'       => get_option( 'emol_frm_app_motivation' ),

			'emol_frm_app_competence' => get_option( 'emol_frm_app_competence' ),

			'emol_warning_already_applyed' => get_option( 'emol_warning_already_applyed' ),

			//company fields
			'emol_frm_com_cname'           => get_option( 'emol_frm_com_cname' ),
			'emol_frm_com_name'            => get_option( 'emol_frm_com_name' ),

			'emol_frm_com_firstname'  => get_option( 'emol_frm_com_firstname' ),
			'emol_frm_com_middlename' => get_option( 'emol_frm_com_middlename' ),
			'emol_frm_com_lastname'   => get_option( 'emol_frm_com_lastname' ),

			'emol_frm_com_contactvia' => get_option( 'emol_frm_com_contactvia' ),

			'emol_frm_com_addr' => get_option( 'emol_frm_com_addr' ),

			'emol_frm_com_street'      => get_option( 'emol_frm_com_street' ),
			'emol_frm_com_housenumber' => get_option( 'emol_frm_com_housenumber' ),
			'emol_frm_com_extension'   => get_option( 'emol_frm_com_extension' ),
			'emol_frm_com_zipcode'     => get_option( 'emol_frm_com_zipcode' ),
			'emol_frm_com_city'        => get_option( 'emol_frm_com_city' ),

			'emol_frm_com_phone'              => get_option( 'emol_frm_com_phone' ),
			'emol_frm_com_email'              => get_option( 'emol_frm_com_email' ),
			'emol_frm_com_dept'               => get_option( 'emol_frm_com_dept' ),
			'emol_frm_com_coc'                => get_option( 'emol_frm_com_coc' ),
			'emol_frm_com_job'                => get_option( 'emol_frm_com_job' ),
			//'emol_frm_com_captcha'         => get_option('emol_frm_com_captcha'),
			'emol_frm_com_logo'               => get_option( 'emol_frm_com_logo' ),
			'emol_frm_google_captcha_sitekey' => get_option( 'emol_frm_google_captcha_sitekey' ),
			'emol_frm_google_captcha_secret'  => get_option( 'emol_frm_google_captcha_secret' ),
			'emol_frm_avg'                    => get_option( 'emol_frm_avg' ),
		);


		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'
		if ( isset( $_POST[ $hidden_field_name ] ) && $_POST[ $hidden_field_name ] == 'Y' ) {


			$_POST['emol_frm_app_competence'] = array();
			for ( $x = 0; $x < count( $_POST['emol_frm_app_competence_label'] ); $x ++ ) {
				$comptenceRow = array(
					'label'         => $_POST['emol_frm_app_competence_label'][ $x ],
					'competence_id' => $_POST['emol_frm_app_competence_competence_id'][ $x ],
					'formelement'   => $_POST['emol_frm_app_competence_formelement'][ $x ],
					'required'      => $_POST['emol_frm_app_competence_required'][ $x ]
				);

				if ( ! empty( $comptenceRow['competence_id'] ) ) {
					$_POST['emol_frm_app_competence'][] = $comptenceRow;
				}
			}

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

		// get list of all competences
		$emol           = eazymatch_connect();
		$competenceList = emol_tree_to_list( $emol->competence->tree(), ' &nbsp;' );
		array_unshift( $competenceList, array(
			'level' => '1',
			'name'  => '',
			'id'    => ''
		) );

		if ( ! is_array( $eazymatchOptions['emol_frm_app_competence'] ) ) {
			$eazymatchOptions['emol_frm_app_competence'] = array();
		}


		// add empty comptence row
		$eazymatchOptions['emol_frm_app_competence'][] = array(
			'label'         => '',
			'competence_id' => '',
			'formelement'   => 'selectbox', // either selectbox or checkbox
			'required'      => 'yes'
		);

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

                <h2><?php echo EMOL_ADMIN_SETTINGS . ' - ' . EMOL_ADMIN_FORM_APP ?></h2>

                <table class="welcome-panel" style="width: 100%;">
                    <colgroup>
                        <col style="width: 200px;"/>
                        <col style="width: 350px;"/>
                    </colgroup>

                    <tr>
                        <td valign="top"
                            colspan="2"><strong>Google re-captcha</strong></td>
                    </tr>
                    <tr>
                    <tr>
                        <td>Sitekey</td>
                        <td><input type="text" name="emol_frm_google_captcha_sitekey"
                                   value="<?php echo $eazymatchOptions['emol_frm_google_captcha_sitekey']; ?>"></td>
                    </tr>
                    <tr>
                        <td>Secret</td>
                        <td><input type="text" name="emol_frm_google_captcha_secret"
                                   value="<?php echo $eazymatchOptions['emol_frm_google_captcha_secret']; ?>"></td>
                    </tr>
                    <tr>
                        <td valign="top"
                            colspan="2">&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_FIRSTNAME . '/' . EMOL_ACCOUNT_APP_LASTNAME, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_name" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_name'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_name'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_name'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_FIRSTNAME, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_firstname" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_firstname'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_firstname'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_firstname'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_MIDDLENAME, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_middlename" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_middlename'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_middlename'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_middlename'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_LASTNAME, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_lastname" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_lastname'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_lastname'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_lastname'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_ADDRESS, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_address" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_address'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_address'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_address'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_STREET, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_street" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_street'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_street'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_street'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_HOUSENUMBER, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_housenr" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_housenr'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_housenr'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_housenr'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_EXTENSION, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_extension" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_extension'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_extension'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_extension'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ZIPCODE, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_zipcode" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_zipcode'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_zipcode'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_zipcode'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_CITY, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_city" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_city'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_city'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_city'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_COUNTRY, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_country" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_country'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_country'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_country'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_BIRTHDATE, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_birthdate" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_birthdate'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_birthdate'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_birthdate'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_BSN, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_ssn" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_ssn'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_ssn'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_ssn'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_MARITALSTATUS, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_maritalstatus" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_maritalstatus'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_maritalstatus'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_maritalstatus'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_NATIONALITY, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_nationality" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_nationality'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_nationality'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_nationality'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_TITLE, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_title" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_title'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_title'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_title'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_CV, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_cv" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_cv'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_cv'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_cv'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_PHOTO, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_photo" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_photo'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_photo'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_photo'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_EMAIL, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_email" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_email'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_email'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_email'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_PHONE, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_phone" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_phone'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_phone'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_phone'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_PHONE, 'Emol-3.0-identifier' ); ?> 2</td>
                        <td>
                            <select name="emol_frm_app_phone2" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_phone2'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_phone2'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_phone2'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_MANAGERCOMPANY, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_app_managercompany" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_managercompany'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_managercompany'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_managercompany'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_PASSWORD, 'Emol-3.0-identifier' ); ?> (beta)</td>
                        <td>
                            <select name="emol_frm_app_password" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_password'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_password'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_password'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Matchprofiel</td>
                        <td>
                            <table cellspacing="0" cellpadding="0">

                                <tr>

                                    <th><?php echo EMOL_ACCOUNT_APP_COMPETENCE_GROUP ?></th>
                                    <th><?php echo EMOL_ACCOUNT_APP_COMPETENCE_FORMELEMENT ?></th>
                                    <th><?php echo EMOL_ACCOUNT_APP_COMPETENCE_LABEL ?></th>
                                    <th><?php echo EMOL_REQ ?></th>
                                </tr>
								<?php foreach ( $eazymatchOptions['emol_frm_app_competence'] as $formcompetence ): ?>
                                    <tr>

                                        <td valign="top">
                                            <select name="emol_frm_app_competence_competence_id[]" style="width: 100%">
												<?php foreach ( $competenceList as $competence ): ?>
                                                    <option
                                                            value="<?php echo $competence['id'] ?>"<?php echo $formcompetence['competence_id'] == $competence['id'] ? ' selected="selected"' : '' ?><?php echo $competence['level'] == 0 ? ' disabled="disabled"' : '' ?>><?php echo $competence['name'] ?></option>
												<?php endforeach ?>
                                            </select>
                                        </td>
                                        <td valign="top">
                                            <select name="emol_frm_app_competence_formelement[]" style="width: 100%">
                                                <option
                                                        value="selectbox"<?php echo $formcompetence['formelement'] == 'selectbox' ? ' selected="selected"' : '' ?>>
                                                    selectbox
                                                </option>
                                                <option
                                                        value="checkbox"<?php echo $formcompetence['formelement'] == 'checkbox' ? ' selected="selected"' : '' ?>>
                                                    checkbox
                                                </option>
                                            </select>
                                        </td>
                                        <td valign="top"><input type="text" name="emol_frm_app_competence_label[]"
                                                                value="<?php echo $formcompetence['label'] ?>"
                                                                style="width: 100%"/></td>
                                        <td valign="top">
                                            <select name="emol_frm_app_competence_required[]" style="width: 100%">
                                                <option
                                                        value="yes"<?php echo $formcompetence['required'] == 'yes' ? ' selected="selected"' : '' ?>><?php echo EMOL_YES ?></option>
                                                <option
                                                        value="no"<?php echo $formcompetence['required'] == 'no' ? ' selected="selected"' : '' ?>><?php echo EMOL_NO ?></option>
                                            </select>
                                        </td>
                                    </tr>
								<?php endforeach ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_SCHOOLING_TYPE_HIGH_LABEL, 'Emol-3.0-identifier' ); ?></td>
                        <td>
                            <select name="emol_frm_app_schoolingtype_id" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_schoolingtype_id'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_schoolingtype_id'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_schoolingtype_id'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_AVAILABLEHOURS, 'Emol-3.0-identifier' ); ?></td>
                        <td>
                            <select name="emol_frm_app_availablehours" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_availablehours'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_availablehours'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_availablehours'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_SALARY, 'Emol-3.0-identifier' ); ?></td>
                        <td>
                            <select name="emol_frm_app_salary" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_salary'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_salary'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_salary'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_SEARCHLOCATION, 'Emol-3.0-identifier' ); ?></td>
                        <td>
                            <select name="emol_frm_app_searchlocation" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_salary'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_salary'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_salary'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ACCOUNT_APP_MOTIVATION, 'Emol-3.0-identifier' ); ?></td>
                        <td>
                            <select name="emol_frm_app_motivation" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_motivation'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_motivation'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_app_motivation'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td valign="top"><?php _e( EMOL_ACCOUNT_APP_CONTACTVIA, 'Emol-3.0-identifier' ); ?><br/>
                            <small>(google,bing,friend,our other site,etc)</small>
                        </td>
                        <td>
                            <textarea name="emol_frm_app_contactvia"
                                      style="width: 100%"><?php echo $eazymatchOptions['emol_frm_app_contactvia'] ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><?php _e( EMOL_WARNING_ALREADY_APPLYED, 'Emol-3.0-identifier' ); ?><br/></td>
                        <td>
                            <textarea name="emol_warning_already_applyed"
                                      style="width: 100%"><?php echo $eazymatchOptions['emol_warning_already_applyed'] ?></textarea>
                        </td>
                    </tr>

                </table>

                <br/>

                <h2><?php echo EMOL_ADMIN_SETTINGS . ' - ' . EMOL_ADMIN_FORM_COMPANY ?></h2>

                <table class="welcome-panel" style="width: 100%;">
                    <colgroup>
                        <col style="width: 200px;"/>
                        <col style="width: 350px;"/>
                    </colgroup>
                    <tr>
                        <td><?php _e( EMOL_REACT_COMPANY, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_cname" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_cname'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_cname'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_cname'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_FIRSTNAME . '/' . EMOL_LASTNAME, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_name" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_name'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_name'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_name'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_FIRSTNAME, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_firstname" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_firstname'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_firstname'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_firstname'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_MIDDLENAME, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_middlename" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_middlename'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_middlename'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_middlename'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_LASTNAME, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_lastname" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_lastname'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_lastname'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_lastname'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_ADDRESS, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_addr" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_addr'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_addr'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_addr'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_STREET, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_street" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_street'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_street'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_street'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_HOUSENUMBER, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_housenumber" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_housenumber'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_housenumber'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_housenumber'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_EXTENSION, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_extension" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_extension'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_extension'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_extension'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_ZIPCODE, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_zipcode" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_zipcode'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_zipcode'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_zipcode'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_CITY, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_city" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_city'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_city'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_city'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_PHONE, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_phone" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_phone'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_phone'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_phone'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_EMAIL, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_email" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_email'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_email'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_email'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_REACT_DEPARTMENT, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_dept" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_dept'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_dept'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_dept'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>


                    <tr>
                        <td><?php _e( EMOL_REACT_COC, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_coc" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_coc'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_coc'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_coc'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>


                    <tr>
                        <td><?php _e( EMOL_REACT_JOB, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_job" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_job'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_job'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_job'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_REACT_LOGO, 'Emol-3.0-identifier' ); ?> </td>
                        <td>
                            <select name="emol_frm_com_logo" style="width: 100%">
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_logo'] == '' ) {
									echo 'selected="selected"';
								} ?> value=''><?php echo EMOL_NO ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_logo'] == 'yes' ) {
									echo 'selected="selected"';
								} ?> value='yes'><?php echo EMOL_YES ?></option>
                                <option <?php if ( (string) $eazymatchOptions['emol_frm_com_logo'] == 'yes_req' ) {
									echo 'selected="selected"';
								} ?> value='yes_req'><?php echo EMOL_YES_REQ ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td valign="top"><?php _e( EMOL_REACT_VIA, 'Emol-3.0-identifier' ); ?><br/>
                            <small>(google,bing,friend,our other site,etc)</small>
                        </td>
                        <td>
                            <textarea name="emol_frm_com_contactvia"
                                      style="width: 100%"><?php echo $eazymatchOptions['emol_frm_com_contactvia'] ?></textarea>
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