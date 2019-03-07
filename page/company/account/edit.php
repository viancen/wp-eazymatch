<?php

/**
 * login and edit a company profile
 */
class emol_page_company_account_edit extends emol_pagedummy {
	/**
	 * The slug for the fake post.  This is the URL for your plugin, like:
	 * http://site.com/about-me or http://site.com/?page_id=about-me
	 * @var string
	 */
	var $page_slug = '';

	//image container
	var $emol_app_image = '';

	/**
	 * The title for your fake post.
	 * @var string
	 */
	var $page_title = 'EmolCompanyAccountPage';

	/**
	 * Allow pings?
	 * @var string
	 */
	var $ping_status = 'open';

	/**
	 * Function to be executed in eazymatch
	 *
	 * @var mixed
	 */
	var $emol_function = '';

	/**
	 * EazyMatch 3.0 Api
	 *
	 * @var mixed
	 */
	var $emolApi;
	var $ws;
	var $user;

	var $msag;

	/**
	 * Class constructor
	 */
	function __construct( $slug, $function = '' ) {

		$this->page_slug     = $slug . '/' . $function;
		$this->emol_function = $function;

		//first connect to the api
		$this->emolApi = eazymatch_connect();

		if ( ! $this->emolApi ) {
			eazymatch_trow_error();
		}

		$this->page_title = EMOL_ACCOUNT_TITLE;

		$this->ws = $this->emolApi->get( 'company' );

		/**
		 * We'll wait til WordPress has looked for posts, and then
		 * check to see if the requested url matches our target.
		 */
		add_filter( 'the_posts', array( &$this, 'detectPost' ) );
	}


	/**
	 * Called by the 'detectPost' action
	 */
	function createPost() {

		/**
		 * What we are going to do here, is create a fake post.  A post
		 * that doesn't actually exist. We're gonna fill it up with
		 * whatever values you want.  The content of the post will be
		 * the output from your plugin.
		 */

		/**
		 * Create a fake post.
		 */
		$post              = new stdClass;
		$post->post_type   = '';
		$post->post_parent = '';

		/**
		 * The author ID for the post.  Usually 1 is the sys admin.  Your
		 * plugin can find out the real author ID without any trouble.
		 */
		$post->post_author = 1;

		/**
		 * The safe name for the post.  This is the post slug.
		 */
		$post->post_name = $this->page_slug;

		/**
		 * Not sure if this is even important.  But gonna fill it up anyway.
		 */
		$post->guid = get_bloginfo( 'wpurl' ) . '/' . $this->page_slug;


		/**
		 * This is the content of the post.  This is where the output of
		 * your plugin should go.  Just store the output from all your
		 * plugin function calls, and put the output into this var.
		 */

		// remove auto line breaks
		remove_filter( 'the_content', 'wpautop' );

		// prepare client resources
		emol_require::validation();
		emol_require::jqueryUi();

		if ( $this->emol_function == 'login' ) {
			$post->post_content = $this->doLogin();
		} elseif ( $this->emol_function == 'logout' ) {
			delete_option( 'emol_apihash' ); //hack, fix
			$post->post_content = $this->doLogout();
		} elseif ( $this->emol_function == 'edit' ) {
			$this->page_title   .= ' - ' . EMOL_MENU_COMP_NAW;
			$post->post_content = $this->getNAWContent();
		} elseif ( $this->emol_function == 'jobs' ) {
			$this->page_title   .= ' - ' . EMOL_MENU_COMP_JOBS;
			$post->post_content = $this->getJobContent();
		} elseif ( $this->emol_function == 'applications' ) {
			$this->page_title   .= ' - ' . EMOL_MENU_APP_APLIC;
			$post->post_content = $this->getApplicationsContent();
		}

		/**
		 * The title of the page.
		 */
		$post->post_title = $this->page_title;

		/**
		 * Fake post ID to prevent WP from trying to show comments for
		 * a post that doesn't really exist.
		 */
		$post->ID = null;

		/**
		 * Static means a page, not a post.
		 */
		$post->post_status = 'static';

		/**
		 * Turning off comments for the post.
		 */
		$post->comment_status = 'closed';

		/**
		 * Let people ping the post?  Probably doesn't matter since
		 * comments are turned off, so not sure if WP would even
		 * show the pings.
		 */
		$post->ping_status = $this->ping_status;

		$post->comment_count = 0;

		/**
		 * You can pretty much fill these up with anything you want.  The
		 * current date is fine.  It's a fake post right?  Maybe the date
		 * the plugin was activated?
		 */
		$post->post_date     = current_time( 'mysql' );
		$post->post_date_gmt = current_time( 'mysql', 1 );

		return ( $post );
	}


	/**
	 * when someone has hit the button to login
	 */
	function doLogin() {

		session_start();


		$wsLogin  = $this->emolApi->get( 'session' );
		$userPass = $_POST['password'];
		$userName = $_POST['username'];

		$userKey = $wsLogin->getUserToken( $userName, $userPass, null );
		if ( $userKey != '' ) {
			$connectionManager = emol_connectManager::getInstance();
			$connectionManager->setUserToken( $userKey );

			//reconnect with new credentials
			$this->emolApi = eazymatch_connect();

			//get user info
			$user       = $this->emolApi->get( 'person' );
			$this->user = $user->getCurrent();

			emol_session::set( array(
				'applicant_id' => $this->user['isApplicant'],
				'company_id'   => $this->user['isCompany'],
				'contact_id'   => $this->user['isContact'],
				'person_id'    => $this->user['id']
			) );

			if ( $user['isCompany'] > 0 ) {
				wp_redirect( '/' . get_option( 'emol_company_account_url' ) . '/edit/' );
			} else {
				wp_redirect( '/' . get_option( 'emol_account_url' ) . '/edit/' );
			}
			exit;

		} else {
			emol_session::terminate();

			return '<p>Uw logingegevens zijn niet juist...</p>';
		}
	}

	/**
	 * when someone has hit the button to lgggout
	 */
	function doLogout() {
		emol_session::terminate();
		//reset emol
		$em = emol_connectManager::getInstance();
		$em->resetUserConnection();

		ob_clean();
		header( 'location: /' );
		exit();
	}


	/**
	 * creates the fake content
	 */
	function getNAWContent() {
		// prepare client resources
		emol_require::validation();
		emol_require::jqueryUi();

		//get the data of this logged on person
		$comp = $this->ws->getSummaryPrivateCompany();
		$list = $this->emolApi->get( 'list' );

		//get user info
		$user    = $this->emolApi->get( 'contact' );
		$contact = $user->getPrivate();

		$comp['Contact'] = $contact;
		//emol_debug($comp);
		//set ids
		$companyId = $comp['id'];
		$personId  = $comp['Contact']['person_id'];
		$contactId = $comp['Contact']['id'];

		//try and get a previous mutation
		$mutation = array();
		try {
			$mutation = $this->ws->getMutationData( 'company-update-naw', $companyId );
			if ( isset( $mutation['content'] ) ) {
				/**manipulate the mutation**/
				$mutation = $mutation['content'];
				if ( count( $mutation['Addresses'] ) > 0 ) {
					$mutation['Preferedaddress'] = $mutation['Addresses'][0];
					unset( $mutation['Addresses'] );
				}
				if ( count( $mutation['Emailaddresses'] ) > 0 ) {
					$mutation['Preferedemailaddress'] = $mutation['Emailaddresses'][0];
					unset( $mutation['Emailaddresses'] );
				}
				if ( count( $mutation['Phonenumbers'] ) > 0 ) {
					$mutation['Preferedphonenumber'] = $mutation['Phonenumbers'][0];
					unset( $mutation['Phonenumbers'] );
				}
			}

		} catch ( SoapFault $e ) {
			eazymatch_trow_error( 'EazyMatch Error: account - company mutations' );
		}

		/**
		 * Save an update of the naw data
		 */
		if ( isset( $_POST['save'] ) && $_POST['save'] == 1 ) {
			$this->saveNAW( $companyId );
		}

		/**
		 * Replace person data with mutation data if the user has given us a mutation already
		 */
		$mutationMsg = '';
		if ( count( $mutation ) > 0 ) {
			$mutationMsg = '<tr><td colspan="2"><div class="emol-account-message">' . EMOL_ACCOUNT_COMP_MSG_MUTATION . '</div></td></tr>';

			$comp = array_merge_recursive_distinct( $comp, $mutation );

			if ( isset( $mutation['Documents']['Logo']['content'] ) && strlen( $mutation['Documents']['Logo']['content'] ) > 0 ) {
				$pic = $mutation['Documents']['Logo']['content'];
			}
		}

		/**fetch all lists in the system**/
		$lists = $list->all();

		/**companySize**/
		$size = '<select name="companysize_id" class="emol-select" id="emol-companysize">';
		if ( isset( $lists['companySizes'] ) ) {
			foreach ( $lists['companySizes'] as $option ) {
				$sel = '';
				if ( $option['id'] == $comp['companysize_id'] ) {
					$sel = 'selected="selected"';
				}
				$size .= '<option value="' . $option['id'] . '" ' . $sel . '>' . $option['name'] . '</option>';
			}
		}
		$size .= '</select>';

		/**branches**/
		$branche = '<select name="branche_id" class="emol-select" id="emol-branche">';
		if ( isset( $lists['branches'] ) ) {
			foreach ( $lists['branches'] as $option ) {
				$sel = '';
				if ( $option['id'] == $comp['branche_id'] ) {
					$sel = 'selected="selected"';
				}
				$branche .= '<option value="' . $option['id'] . '" ' . $sel . '>' . $option['name'] . '</option>';
			}
		}
		$branche .= '</select>';


		/**
		 * Create the html form
		 *
		 * @var mixed
		 */
		$accountHtml = $mutationMsg . $this->msag . '
                <div id="emol-form-companyaccount" class="emol-form-div">
                        <form method="post" id="emol-account-naw-form" enctype="multipart/form-data">
                        <input type="hidden" name="save" value="1" />
                        <table class="emol-form-table">
                <tbody>';

		$img = '';
		if ( isset( $comp['Logo']['content'] ) ) {
			$img = '<img src="data:image/png;base64,' . $comp['Logo']['content'] . '" width="100"  />';
		} else {
			$img = '<img src="' . get_bloginfo( 'wpurl' ) . '/wp-content/plugins/eazymatch/icon/blank-icon.png" alt="" />';
		}

		$accountHtml .= '
                                <tr id="emol-companyname-row">
                                        <td class="emol-label-wrapper">
                                                <label for="name">' . EMOL_REACT_COMPANY . '</label>
                                        </td>
                                        <td>
                                                <input type="text" class="emol-text-input" name="name" value="' . $comp['name'] . '" id="name" />
                                        </td>
                                </tr>
                                
                                <tr id="emol-address-row">
                                        <td class="emol-label-wrapper">
                                                <label for="emol-address">' . EMOL_ADDRESS . ' + </label>
                                <label for="emol-housenumber">' . EMOL_HOUSENUMBER . '  + </label>
                                <label for="emol-extension">' . EMOL_EXTENSION . '</label>
                                        </td>
                                        <td>
                                                <input type="hidden" class="emol-text-input" name="address[1][id]" value="' . @$comp['Preferedaddress']['id'] . '" />
                                                <input type="text" class="emol-text-input" name="address[1][street]" id="emol-address" value="' . @$comp['Preferedaddress']['street'] . '" />
                                                <input type="text" class="emol-text-input emol-small validate[required,custom[onlyNumber],length[0,5]]" name="address[1][housenumber]" id="emol-housenumber" value="' . @$comp['Preferedaddress']['housenumber'] . '" />
                                                <input type="text" class="emol-text-input emol-small" name="address[1][extension]" id="emol-extension" value="' . @$comp['Preferedaddress']['extension'] . '" />
                                        </td>
                                </tr>
                                
                                <tr id="emol-location-row">
                                        <td class="emol-label-wrapper">
                                                <label for="emol-zipcode">' . EMOL_ZIPCODE . ' + </label>
                                                <label for="emol-city">' . EMOL_CITY . '</label>
                                        </td>
                                        <td>
                                                <input type="text" class="emol-text-input" name="address[1][zipcode]" id="emol-zipcode" value="' . @$comp['Preferedaddress']['zipcode'] . '" />
                                                <input type="text" class="emol-text-input" name="address[1][city]" id="emol-city" value="' . @$comp['Preferedaddress']['city'] . '" />
                                        </td>
                </tr>
                                
                                <tr id="emol-phone-row">
                                        <td class="emol-label-wrapper">
                                                <label for="emol-phonenumber">' . EMOL_PHONE . '</label>
                                        </td>
                                        <td>
                                                <input type="hidden" class="emol-text-input" name="phonenumber[id]" value="' . @$comp['Preferedphonenumber']['id'] . '" />
                                                <input type="text" class="emol-text-input" name="phonenumber[phonenumber]" id="emol-phonenumber" value="' . @$comp['Preferedphonenumber']['phonenumber'] . '" />
                                        </td>
                </tr>
                                
                <tr id="emol-email-row">
                                        <td class="emol-label-wrapper">
                                                <label for="emol-email">' . EMOL_EMAIL . '</label>
                                        </td>
                                        <td>
                                                <input type="hidden" class="emol-text-input" name="email[id]" value="' . @$comp['Preferedemailaddress']['id'] . '" />
                                                <input type="text" class="emol-text-input" name="email[email]" id="emol-email" value="' . @$comp['Preferedemailaddress']['email'] . '" />
                                        </td>
                </tr>
                                
                                <tr id="emol-coc-row">
                                        <td class="emol-label-wrapper">
                                                <label for="emol-coc">' . EMOL_REACT_COC . '</label>
                                        </td>
                                        <td>
                                                <input type="text" class="emol-text-input" name="coc" id="emol-coc" value="' . @$comp['coc'] . '" />
                                        </td>
                                </tr>
                                
                                <tr id="emol-branche-row">
                                        <td class="emol-label-wrapper">
                                                <label for="emol-branche">' . EMOL_REACT_BRANCHE . '</label>
                                        </td>
                                        <td>
                                                ' . $branche . '
                                        </td>
                                </tr>
                                
                                <tr id="emol-companysize-row">
                                        <td class="emol-label-wrapper">
                                                <label for="emol-companysize">' . EMOL_REACT_SIZE . '</label>
                                        </td>
                                        <td>
                                                ' . $size . '
                                        </td>
                                </tr>
                                
                                <tr id="emol-profile-row">
                                        <td class="emol-label-wrapper">
                                                <label for="emol-profile">' . EMOL_REACT_BPROFILE . '</label>
                                        </td>
                                        <td>
                                                <textarea type="text" class="emol-textarea" name="profile" id="emol-profile">' . strip_tags( $comp['profile'] ) . '</textarea>
                                        </td>
                                </tr>
                                
                                <tr id="emol-logo-row">
                                        <td>
                                                <label for="emol-logo">' . EMOL_REACT_LOGO . '</label>
                                        </td>
                                        <td>
                                                <input type="file" class="emol-text-input emol-file" name="logo" id="emol-logo" /><br />
                                                ' . $img . '
                                        </td>
                                </tr>
                                
                                <tr id="emol-submit-row">
                                        <td>
                                                &nbsp;
                                        </td>
                                        <td>
                                                <input type="submit" class="emol-button emol-button-submit emol-button-editcompany" value="' . EMOL_ACCOUNT_SAVE . '" />
                                        </td>
                                </tr>
                        ';


		//finish up html
		$accountHtml .= '</tbody>
                </table>
                </form>
                </div>';


		return $accountHtml;
	}

	/**
	 * creates the fake content
	 */
	function getJobContent() {

		try {
			//get the data of this logged on person
			$comp = $this->ws->getSummaryPrivateCompany();
			$list = $this->emolApi->get( 'job' );

			//get user info
			$user    = $this->emolApi->get( 'contact' );
			$contact = $user->getPrivate();

		} catch ( SoapFault $e ) {
			$this->doLogout();
		}

		$comp['Contact'] = $contact;


		$companyId = $comp['id'];
		$personId  = $comp['Contact']['person_id'];
		$contactId = $comp['Contact']['id'];

		//get all jobs
		$jobs = $list->getPrivate();

		//html with jobs
		$accountHtml = '<table class="emol-account-table">
                <tr class="emol-account-table-header">
                        <td>#</td>
                        <td>' . EMOL_JOB_NAME . '</td>
                        <td>' . EMOL_JOB_START . '</td>
                        <td>' . EMOL_JOB_END . '</td>
                        <td>' . EMOL_JOB_STATE . '</td>
                </tr>';

		if ( count( $jobs ) > 0 ) {
			foreach ( $jobs as $job ) {

				if (
					$job['active'] == 1 &&
					$job['published'] == 1 &&
					date( 'Ymd', strtotime( $job['startdate'] ) ) <= date( 'Ymd' ) &&
					date( 'Ymd', strtotime( $job['enddate'] ) ) >= date( 'Ymd' ) &&
					date( 'Ymd', strtotime( $job['startpublished'] ) ) <= date( 'Ymd' ) &&
					date( 'Ymd', strtotime( $job['endpublished'] ) ) >= date( 'Ymd' )
				) {
					$state = '<font color=green>' . EMOL_ONLINE . '</font>';
				} else {
					$state = '<font color=red>' . EMOL_OFFLINE . '</font>';
				}

				if ( is_null( $job['startpublished'] ) ) {
					$job['startpublished'] = '-';
				} else {
					$job['startpublished'] = date( 'd-m-Y', strtotime( $job['startpublished'] ) );
				}
				if ( is_null( $job['endpublished'] ) ) {
					$job['endpublished'] = '-';
				} else {
					$job['endpublished'] = date( 'd-m-Y', strtotime( $job['endpublished'] ) );
				}

				$accountHtml .= '<tr>';
				$accountHtml .= '<td>#' . $job['id'] . '</td>';
				$accountHtml .= '<td>' . $job['name'] . '</td>';
				$accountHtml .= '<td>' . $job['startpublished'] . '</td>';
				$accountHtml .= '<td>' . $job['endpublished'] . '</td>';
				$accountHtml .= '<td>' . $state . '</td>';
				$accountHtml .= '</tr>';
			}
		}
		$accountHtml .= '</table>';

		return $accountHtml;
	}

	/**
	 * creates the fake content
	 */
	function getApplicationsContent() {
		// prepare client resources
		emol_require::validation();
		emol_require::jqueryUi();

		try {
			$this->wsApp = $this->emolApi->get( 'mediation' );
			$med         = $this->wsApp->byCompanyPrivate();
		} catch ( SoapFault $e ) {
			$this->doLogout();
		}

		$medContent = '<table class="emol-account-table">
                <tr class="emol-account-table-header">
                        <td>' . EMOL_APPL_DATE . '</td>
                        <td>' . EMOL_APPL_APPTITLE . '</td>
                        <td>' . EMOL_APPL_JOB . '</td>
                        <td>' . EMOL_APPL_STATE . '</td>
                </tr>';
		foreach ( $med as $mediation ) {


			$status = '-';
			if ( isset( $mediation['Mediationphase']['name'] ) ) {
				$status = $mediation['Mediationphase']['name'];
			}

			$desc = EMOL_ACCOUNT_EMPTY;
			if ( $mediation['description'] > '' ) {
				$desc = $mediation['description'];
			}

			$medContent .= '<tr>';
			$medContent .= '<td>' . date( 'd-m-Y H:i', strtotime( $mediation['datemodified'] ) ) . '</td>';
			$medContent .= '<td><span class="emol-mediation-title">(#' . $mediation['Applicant']['id'] . ') ' . $mediation['Applicant']['title'] . '</span></td>';
			$medContent .= '<td><span class="emol-mediation-title">(#' . $mediation['Job']['id'] . ') ' . $mediation['Job']['name'] . '</span></td>';
			$medContent .= '<td>' . $status . '</td>';
			$medContent .= '</tr>';
			$medContent .= '<tr>';
			$medContent .= '<td colspan="4"><div class="emol-mediation-description">' . $desc . '</div></td>';
			$medContent .= '</tr>';
		}
		$medContent .= '</table>';

		return $medContent;
	}

	/**
	 * Save NAW to emol 3
	 *
	 */
	function saveNAW( $companyId ) {
		//save me
		//create a array the way EazyMatch likes it
		$subscription = new emol_CompanyMutation();

		//set the Company
		$subscription->setCompany(
			$companyId,
			emol_post( 'name' ),
			emol_post( 'profile' ),
			emol_post( 'companysize_id' ),
			emol_post( 'branche_id' ),
			emol_post( 'coc' )
		);

		//set addresses
		foreach ( $_POST['address'] as $addrPieceArr ) {
			$addrPiece = new emol_array( $addrPieceArr );

			$subscription->addAddress(
				$addrPiece->id,
				$addrPiece->province_id,
				$addrPiece->country_id,
				$addrPiece->region_id,
				$addrPiece->street,
				$addrPiece->housenumber,
				$addrPiece->extension,
				$addrPiece->zipcode,
				$addrPiece->city
			);
		}

		/**email**/
		$subscription->addEmailaddresses( $_POST['email']['id'], null, $_POST['email']['email'] );

		/**phonenumber**/
		$subscription->addPhonenumber( $_POST['phonenumber']['id'], null, $_POST['phonenumber']['phonenumber'] );

		/**PHOTO**/
		if ( isset( $_FILES['logo']['tmp_name'] ) && (string) $_FILES['logo']['tmp_name'] != '' ) {

			/**set the CV document**/
			$doc            = array();
			$doc['name']    = $_FILES['logo']['name'];
			$doc['content'] = base64_encode( file_get_contents( $_FILES['logo']['tmp_name'] ) );
			$doc['type']    = $_FILES['logo']['type'];

			/**set the logo**/
			$subscription->setLogo( $doc['name'], $doc['type'], $doc['content'] );
		}

		/**create the workable postable array**/
		$postData = $subscription->createSubscription();


		/**save the subscription to EazyMatch**/
		$da = $this->ws->addMutationData( $postData, 'company-update-naw' );

		ob_clean();
		header( 'location: /' . get_option( 'emol_company_account_url' ) . '/edit/' );
		exit();
	}

	/**
	 * userd by the initialisation
	 */
	function detectPost( $posts ) {
		global $wp;
		global $wp_query;
		/**
		 * Check if the requested page matches our target
		 */
		if ( strtolower( $wp->request ) == strtolower( $this->page_slug ) || $wp->query_vars['page_id'] == $this->page_slug ) {
			//Add the fake post
			$posts   = null;
			$posts[] = $this->createPost();

			/**
			 * Trick wp_query into thinking this is a page (necessary for wp_title() at least)
			 * Not sure if it's cheating or not to modify global variables in a filter
			 * but it appears to work and the codex doesn't directly say not to.
			 */
			$wp_query->is_page = true;
			//Not sure if this one is necessary but might as well set it like a true page
			$wp_query->is_singular = true;
			$wp_query->is_home     = false;
			$wp_query->is_archive  = false;
			$wp_query->is_category = false;
			//Longer permalink structures may not match the fake post slug and cause a 404 error so we catch the error here
			unset( $wp_query->query["error"] );
			$wp_query->query_vars["error"] = "";
			$wp_query->is_404              = false;

		}

		return $posts;
	}
}