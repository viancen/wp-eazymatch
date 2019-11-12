<?php
function eazymatch_plugin_job() {

	flush_rewrite_rules( true );

	//must check that the user has the required capability
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	if ( ! get_option( 'emol_apihash' ) ) {
		echo "<div style=\"background-color:#ffebeb;border:1px solid red;margin-top:10px;text-align:center;font-weight:bold;padding:15px;\">FIRST SETUP YOUR CREDENTIALS UNDER GLOBAL</div>";
	} else {

		$emol_api = eazymatch_connect();
		$jobs     = $emol_api->get( 'job' )->searchPublished( array(), 0, 10 );

		$trunk = new emol_trunk();

		// create a response array and add all the requests to the trunk
		$jobStatus      = &$trunk->request( 'tree', 'tree', array( 'Jobstatus' ) );
		$jobTexts       = &$trunk->request( 'form', 'getJobTextDescription' );
		$competenceTree = &$trunk->request( 'competence', 'tree' );

		// execute the trunk request
		$trunk->execute();

		// variables for the field and option names
		$hidden_field_name = 'mt_submit_hidden';

		$eazymatchOptions = array(
			'emol_job_header'                 => get_option( 'emol_job_header' ),
			'emol_job_url'                    => get_option( 'emol_job_url' ),
			'emol_job_page'                   => get_option( 'emol_job_page' ),
			'emol_job_amount_pp'              => get_option( 'emol_job_amount_pp' ),
			'emol_job_search_url'             => get_option( 'emol_job_search_url' ),
			'emol_job_search_page'            => get_option( 'emol_job_search_page' ),
			'emol_job_search_logo'            => get_option( 'emol_job_search_logo' ),
			'emol_job_search_date'            => get_option( 'emol_job_search_date' ),
			'emol_job_search_hours'           => get_option( 'emol_job_search_hours' ),
			'emol_job_search_startdate'       => get_option( 'emol_job_search_startdate' ),
			'emol_job_search_enddate'         => get_option( 'emol_job_search_enddate' ),
			'emol_job_search_desc'            => get_option( 'emol_job_search_desc' ),
			'emol_job_search_region'          => get_option( 'emol_job_search_region' ),
			'emol_job_search_city'            => get_option( 'emol_job_search_city' ),
			'emol_job_offline'                => get_option( 'emol_job_offline' ),
			'emol_job_no_result'              => get_option( 'emol_job_no_result' ),
			'emol_apply_process_directly'     => get_option( 'emol_apply_process_directly' ),
			'emol_apply_url'                  => get_option( 'emol_apply_url' ),
			'emol_apply_url_success_redirect' => get_option( 'emol_apply_url_success_redirect' ),
			'emol_apply_email'                => get_option( 'emol_apply_email' ),
			'emol_apply_email_text'           => get_option( 'emol_apply_email_text' ),
			'emol_apply_url_free'             => get_option( 'emol_apply_url_free' ),
			'emol_job_texts'                  => get_option( 'emol_job_texts' ),
			'emol_apply_page'                 => get_option( 'emol_apply_page' ),
			'emol_job_competence_exclude'     => get_option( 'emol_job_competence_exclude' ),
			//'emol_filter_options'           => get_option('emol_filter_options'),
			'emol_job_search_competence'      => get_option( 'emol_job_search_competence' ),
			'emol_apply_success'              => get_option( 'emol_apply_success' )
		);


		// get the default filter options
		$filter = emol_jobfilter_factory::createDefault();

		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'
		if ( isset( $_POST[ $hidden_field_name ] ) && $_POST[ $hidden_field_name ] == 'Y' ) {


			$_POST['emol_job_search_competence'] = array();
			for ( $x = 0; $x < count( $_POST['emol_job_search_competence_label'] ); $x ++ ) {

				$comptenceRow = array(
					'label'         => $_POST['emol_job_search_competence_label'][ $x ],
					'competence_id' => $_POST['emol_job_search_competence_id'][ $x ],
				);

				if ( ! empty( $comptenceRow['competence_id'] ) ) {
					$_POST['emol_job_search_competence'][] = $comptenceRow;
				}
			}

			if ( ! is_numeric( $_POST['emol_job_amount_pp'] ) ) {
				$_POST['emol_job_amount_pp'] = 5;
			}

			// apply default filter options
			if ( ! isset( $_POST['filter_competence'] ) ) {
				$_POST['filter_competence'] = array();
			}
			$filter->setCompetence( $_POST['filter_competence'] );

			if ( ! isset( $_POST['filter_status'] ) ) {
				$_POST['filter_status'] = array();
			}
			$filter->setStatus( $_POST['filter_status'] );

			update_option( 'emol_jobfilter_default', $filter );


			// save all options defined earlier
			foreach ( $_POST as $option => $value ) {
				if ( ! array_key_exists( $option, $eazymatchOptions ) ) {
					continue;
				}

				if ( $option == 'emol_job_texts' ) {
					$value = serialize( $value );
				}

				update_option( $option, $value );
				$eazymatchOptions[ $option ] = $value;
			}

			?>
            <div class="updated"><p><strong><?php _e( EMOL_ADMIN_SAVEMSG, 'Emol-3.0-identifier' ); ?></strong></p></div>
			<?php
		}

		//checkbox for picture on/off
		$sel2 = 'checked="checked"';
		$sel1 = '';

		if ( get_option( 'emol_job_search_logo' ) == 1 ) {
			$sel1 = 'checked="checked"';
			$sel2 = '';
		}
		$checkboxPicture = '<input type="radio" name="emol_job_search_logo" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
		$checkboxPicture .= '<input type="radio" name="emol_job_search_logo" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

		$sel2 = 'checked="checked"';
		$sel1 = '';

		//description
		if ( get_option( 'emol_job_search_desc' ) == 1 ) {
			$sel1 = 'checked="checked"';
			$sel2 = '';
		}
		$checkboxDescr = '<input type="radio" name="emol_job_search_desc" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
		$checkboxDescr .= '<input type="radio" name="emol_job_search_desc" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

		$sel2 = 'checked="checked"';
		$sel1 = '';

		//placementdate
		if ( get_option( 'emol_job_search_date' ) == 1 ) {
			$sel1 = 'checked="checked"';
			$sel2 = '';
		}
		$checkboxDate = '<input type="radio" name="emol_job_search_date" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
		$checkboxDate .= '<input type="radio" name="emol_job_search_date" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

		$sel2 = 'checked="checked"';
		$sel1 = '';

		//hours
		if ( get_option( 'emol_job_search_hours' ) == 1 ) {
			$sel1 = 'checked="checked"';
			$sel2 = '';
		}
		$checkboxHours = '<input type="radio" name="emol_job_search_hours" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
		$checkboxHours .= '<input type="radio" name="emol_job_search_hours" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

		$sel2 = 'checked="checked"';
		$sel1 = '';

		//emol_job_search_startdate
		if ( get_option( 'emol_job_search_startdate' ) == 1 ) {
			$sel1 = 'checked="checked"';
			$sel2 = '';
		}
		$checkboxStartdate = '<input type="radio" name="emol_job_search_startdate" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
		$checkboxStartdate .= '<input type="radio" name="emol_job_search_startdate" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

		$sel2 = 'checked="checked"';
		$sel1 = '';

		//emol_job_search_enddate
		if ( get_option( 'emol_job_search_enddate' ) == 1 ) {
			$sel1 = 'checked="checked"';
			$sel2 = '';
		}
		$checkboxEnddate = '<input type="radio" name="emol_job_search_enddate" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
		$checkboxEnddate .= '<input type="radio" name="emol_job_search_enddate" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';

		$sel2 = 'checked="checked"';
		$sel1 = '';

		//emol_job_search_region
		if ( get_option( 'emol_job_search_region' ) == 1 ) {
			$sel1 = 'checked="checked"';
			$sel2 = '';
		}
		$checkboxRegio = '<input type="radio" name="emol_job_search_region" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
		$checkboxRegio .= '<input type="radio" name="emol_job_search_region" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';


		//emol_job_search_city
		if ( get_option( 'emol_job_search_city' ) == 1 ) {
			$sel1 = 'checked="checked"';
			$sel2 = '';
		}
		$checkboxCity = '<input type="radio" name="emol_job_search_city" value="1" ' . $sel1 . ' /> ' . EMOL_ON . ' &nbsp;';
		$checkboxCity .= '<input type="radio" name="emol_job_search_city" value="0" ' . $sel2 . ' /> ' . EMOL_OFF . '';


		$competenceList = emol_tree_to_list( $emol_api->competence->tree(), ' &nbsp;' );
		array_unshift( $competenceList, array(
			'level' => '1',
			'name'  => '',
			'id'    => ''
		) );

		if ( ! is_array( $eazymatchOptions['emol_job_search_competence'] ) ) {
			$eazymatchOptions['emol_job_search_competence'] = array();
		}

		// add empty comptence row
		$eazymatchOptions['emol_job_search_competence'][] = array(
			'label'         => '',
			'competence_id' => ''
		);

		echo '<div class="wrap">';
		echo "<h2>" . __( 'EazyMatch > ' . EMOL_ADMIN_SETTINGS . ' > ' . EMOL_ADMIN_JOB . ' ', 'Emol-3.0-identifier' ) . "</h2>";
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

        <h3>Shorttags</h3>
        <table class="welcome-panel" style="width: 100%;">
            <thead>
            <tr>
                <td><strong>Shortcode</strong></td>
                <td><strong>Extra attributes voorbeeld</strong></td>
                <td><strong>Toelichting</strong></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td width="250" valign="top">[eazymatch view="searchjobs"]</td>
                <td valign="top">settings="title=Zoeken|button=Zoek vacatures|reset=alle vacatures weergeven|title=Begin
                    hier met het zoeken naar vacatures!"
                </td>
                <td valign="top">Geeft een zoekformulier weer op basis van uw instellingen</td>
            </tr>
            <tr>
                <td width="250" valign="top">[eazymatch view="jobs"]</td>
                <td valign="top">competences="101,102,304,102"</td>
                <td valign="top">Id's uit uw matchprofiel om voorgedefineerde lijsten / urls te maken</td>
            </tr>
            </tbody>
        </table>

        <form name="form1" method="post" action="">
            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

            <div id="emol-admin-table">
                <table class="welcome-panel" style="width: 100%;">
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBDISPLAY_URL, 'Emol-3.0-identifier' ); ?><Br/> <em>(dummy-url: url mag niet bestaan)</em></td>
                        <td><input type="text" name="emol_job_url"
                                   value="<?php echo $eazymatchOptions['emol_job_url']; ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBSEARCH_URL, 'Emol-3.0-identifier' ); ?><Br/> <em>(dummy-url: url mag niet bestaan)</em></td>
                        <td><input type="text" name="emol_job_search_url"
                                   value="<?php echo $eazymatchOptions['emol_job_search_url']; ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_APPLY_URL, 'Emol-3.0-identifier' ); ?><Br/> <em>(dummy-url: url mag niet bestaan)</em></td>
                        <td><input type="text" name="emol_apply_url"
                                   value="<?php echo $eazymatchOptions['emol_apply_url']; ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_ADMIN_APPLY_URL_FREE, 'Emol-3.0-identifier' ); ?><Br/> <em>(dummy-url: url mag niet bestaan)</em></td>
                        <td><input type="text" name="emol_apply_url_free"
                                   value="<?php echo $eazymatchOptions['emol_apply_url_free'] ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>
                </table>

                <table class="welcome-panel"  style="width: 100%;">
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBDISPLAY_PAGE, 'Emol-3.0-identifier' ); ?> <Br>(<strong>Pagina met shortcode [eazymatch view="job"]</strong>)</td>
                        <td><?php

							//id and name of form element should be same as the setting name.
							$args  = array(
								'sort_order'   => 'asc',
								'sort_column'  => 'post_title',
								'hierarchical' => 1,
								'exclude'      => '',
								'include'      => '',
								'meta_key'     => '',
								'meta_value'   => '',
								'authors'      => '',
								'child_of'     => 0,
								'exclude_tree' => '',
								'number'       => '',
								'offset'       => 0,
								'post_type'    => 'page',
								'post_status'  => 'publish,private,draft'
							);
							$pages = get_pages( $args );

							?>

                            <select type="text" class="eazycv-admin-select" name="emol_job_page" style="width:100%;">
                                <option value=""></option>
								<?php
								foreach ( $pages as $page ) { ?>
                                    <option <?php if ( $eazymatchOptions['emol_job_page'] == $page->post_name ) {
										echo 'selected';
									} ?>
                                            value="<?php echo $page->post_name ?>"><?php echo $page->post_title ?> (<?php echo $page->post_name ?>)
                                    </option>
								<?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBSEARCH_PAGE, 'Emol-3.0-identifier' ); ?><Br>(<strong>Pagina met shortcode [eazymatch view="jobpage"]</strong>)</td>
                        <td>
                            <select type="text" class="eazycv-admin-select" name="emol_job_search_page" style="width:100%;">
                                <option value=""></option>
								<?php
								foreach ( $pages as $page ) { ?>
                                    <option <?php if ( $eazymatchOptions['emol_job_search_page'] == $page->post_name ) {
										echo 'selected';
									} ?>
                                            value="<?php echo $page->post_name ?>"><?php echo $page->post_title ?> (<?php echo $page->post_name ?>)
                                    </option>
								<?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_APPLY_PAGE, 'Emol-3.0-identifier' ); ?><Br>(<strong>Pagina met shortcode [eazymatch view="apply"]</strong>)</td>
                        <td>
                            <select type="text" class="eazycv-admin-select" name="emol_apply_page" style="width:100%;">
                                <option value=""></option>
								<?php
								foreach ( $pages as $page ) { ?>
                                    <option <?php if ( $eazymatchOptions['emol_apply_page'] == $page->post_name ) {
										echo 'selected';
									} ?>
                                            value="<?php echo $page->post_name ?>"><?php echo $page->post_title ?> (<?php echo $page->post_name ?>)
                                    </option>
								<?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_APPLY_URL_REDIRECT, 'Emol-3.0-identifier' ); ?> <Br>(<strong>Bedankt pagina</strong>)</td>
                        <td>
                            <select type="text" class="eazycv-admin-select" name="emol_apply_url_success_redirect" style="width:100%;">
                                <option value=""></option>
								<?php
								foreach ( $pages as $page ) { ?>
                                    <option <?php if ( $eazymatchOptions['emol_apply_url_success_redirect'] == $page->post_name ) {
										echo 'selected';
									} ?>
                                            value="<?php echo $page->post_name ?>"><?php echo $page->post_title ?> (<?php echo $page->post_name ?>)
                                    </option>
								<?php } ?>
                            </select>
                        </td>
                    </tr>

                </table>

                <table class="welcome-panel" style="width: 100%;">
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBSEARCH_LOGOS, 'Emol-3.0-identifier' ); ?> </td>
                        <td><?php echo $checkboxPicture; ?></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBSEARCH_DATE, 'Emol-3.0-identifier' ); ?> </td>
                        <td><?php echo $checkboxDate; ?></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBSEARCH_STARTDATE, 'Emol-3.0-identifier' ); ?> </td>
                        <td><?php echo $checkboxStartdate; ?></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBSEARCH_ENDDATE, 'Emol-3.0-identifier' ); ?> </td>
                        <td><?php echo $checkboxEnddate; ?></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBSEARCH_HOURS, 'Emol-3.0-identifier' ); ?> </td>
                        <td><?php echo $checkboxHours; ?></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBSEARCH_DESCR, 'Emol-3.0-identifier' ); ?> </td>
                        <td><?php echo $checkboxDescr; ?></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBSEARCH_REGIO, 'Emol-3.0-identifier' ); ?> </td>
                        <td><?php echo $checkboxRegio; ?></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBSEARCH_CITY, 'Emol-3.0-identifier' ); ?> </td>
                        <td><?php echo $checkboxCity; ?></td>
                        <td>
                        </td>
                    </tr>
                    <Tr>
                        <Td>Competenties</Td>
                        <Td>
                            <table cellspacing="0" cellpadding="0">
                                <colgroup>
                                    <col style="width: 200px;"/>
                                    <col style="width: 120px;"/>
                                </colgroup>
                                <tr>
                                    <th><?php echo EMOL_ACCOUNT_APP_COMPETENCE_GROUP ?></th>
                                    <th>Titel</th>
                                </tr>
								<?php foreach ( $eazymatchOptions['emol_job_search_competence'] as $formcompetence ): ?>
                                    <tr>
                                        <td valign="top">
                                            <select name="emol_job_search_competence_id[]" style="width: 100%">
												<?php foreach ( $competenceList as $competence ): ?>
                                                    <option
                                                            value="<?php echo $competence['id'] ?>"<?php echo $formcompetence['competence_id'] == $competence['id'] ? ' selected="selected"' : '' ?><?php echo $competence['level'] == 0 ? ' disabled="disabled"' : '' ?>><?php echo $competence['name'] ?></option>
												<?php endforeach ?>
                                            </select>
                                        </td>
                                        <td valign="top"><input type="text" name="emol_job_search_competence_label[]"
                                                                value="<?php echo $formcompetence['label'] ?>"
                                                                style="width: 100%"/>
                                        </td>
                                    </tr>
								<?php endforeach ?>
                            </table>
                        </Td>
                        <td></td>
                    </Tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_RESULTSPERPAGE, 'Emol-3.0-identifier' ); ?> </td>
                        <td><input type="text" name="emol_job_amount_pp"
                                   value="<?php echo $eazymatchOptions['emol_job_amount_pp']; ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_JOBHEADER, 'Emol-3.0-identifier' ); ?> </td>
                        <td><input type="text" name="emol_job_header"
                                   value="<?php echo $eazymatchOptions['emol_job_header']; ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="cTdh"><br>

                            <h2><?php echo EMOL_ADMIN_APPLYING ?></h2></td>
                    </tr>
                    <tr>
                        <td><?php _e( EMOL_ADMIN_APPLY_PROCESS_DIRECT, 'Emol-3.0-identifier' ); ?> </td>
                        <td><select name="emol_apply_process_directly">
                                <option value="0" <?php echo $eazymatchOptions['emol_apply_process_directly'] == 0 ? 'selected=selected' : ''; ?>>
                                    Nee, opnemen in webaanmeldingen
                                </option>
                                <option value="1" <?php echo $eazymatchOptions['emol_apply_process_directly'] == 1 ? 'selected=selected' : ''; ?>>
                                    Ja, direct opnemen als kandidaat
                                </option>
                            </select>
                        </td>
                        <td>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_ADMIN_APPLY_EMAIL, 'Emol-3.0-identifier' ); ?> </td>
                        <td><input type="text" name="emol_apply_email"
                                   value="<?php echo $eazymatchOptions['emol_apply_email']; ?>" size="40"></td>
                        <td>
                        </td>
                    </tr>

                    <tr>
                        <td><?php _e( EMOL_ADMIN_APPLY_EMAIL_TEXT, 'Emol-3.0-identifier' ); ?> </td>
                        <td><input type="text" name="emol_apply_email_text"
                                   value="<?php echo stripslashes( $eazymatchOptions['emol_apply_email_text'] ); ?>"
                                   size="40"></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><?php _e( EMOL_ADMIN_MSGNORESULT, 'Emol-3.0-identifier' ); ?> </td>
                        <td><textarea name="emol_job_no_result" cols="62"
                                      rows="7"><?php echo $eazymatchOptions['emol_job_no_result']; ?></textarea></td>
                        <td>
                        </td>
                    </tr>
                    <!--<tr>
                        <td valign="top"><?php _e( EMOL_ADMIN_MSGJOBOFFLINE, 'Emol-3.0-identifier' ); ?> </td>
                        <td><textarea name="emol_job_offline" cols="62" rows="7" ><?php echo $eazymatchOptions['emol_job_offline']; ?></textarea></td><td>
                        </td>
                    </tr>-->
                    <tr>
                        <td valign="top"><?php _e( EMOL_ADMIN_MSGAFTERAPPLY, 'Emol-3.0-identifier' ); ?> </td>
                        <td><textarea name="emol_apply_success" cols="62"
                                      rows="7"><?php echo $eazymatchOptions['emol_apply_success']; ?></textarea></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><?php _e( EMOL_ADMIN_JOBCOMPEXCLUDE, 'Emol-3.0-identifier' ); ?> </td>
                        <td><textarea name="emol_job_competence_exclude" cols="62"
                                      rows="3"><?php echo $eazymatchOptions['emol_job_competence_exclude']; ?></textarea>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="cTdh"><br>
                            <h2><?php echo EMOL_ADMIN_JOB_TEXTS ?></h2></td>
                    </tr>
					<?php
					$currentLabelData = array();
					if ( $eazymatchOptions['emol_job_texts'] != '' ) {
						$currentLabelData = unserialize( $eazymatchOptions['emol_job_texts'] );
					}
					foreach ( $jobTexts as $textarea ) {
						?>
                        <tr>
                            <td><?php echo $textarea['label']; ?></td>
                            <td colspan="2">
                                <input name="emol_job_texts[<?php echo $textarea['label']; ?>]"
                                       value="<?php echo isset( $currentLabelData[ $textarea['label'] ] ) ? $currentLabelData[ $textarea['label'] ] : $textarea['label']; ?>"
                                       type="text"/>
                            </td>
                        </tr>
						<?php
					}
					?>
                    <tr>
                        <td colspan="3" class="cTdh"><br>
                            <h2><?php echo EMOL_ADMIN_FILTERS ?></h2></td>
                    </tr>
                    <tr>
                        <td valign="top">Filters</td>
                        <td colspan="2">
							<?php
							echo @emol_parse_tree( $jobStatus[0]['children'], $filter->getStatus(), array(), 'filter_status[]' );
							?>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">&nbsp;</td>
                        <td colspan="2">
							<?php
							foreach ( $competenceTree as $treeSubject ) {
								echo '<h3>' . $treeSubject['name'] . '</h3>';
								echo @emol_parse_tree( $treeSubject['children'], $filter->getCompetence(), array(), 'filter_competence[]' );
							}
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
