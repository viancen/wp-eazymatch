<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Applying to a job or open or whatever
 */
class emol_page_applicant_apply extends emol_pagedummy {
	/**
	 * The slug for the fake post.  This is the URL for your plugin, like:
	 * http://site.com/about-me or http://site.com/?page_id=about-me
	 * @var string
	 */
	var $page_slug = '';

	/**
	 * The title for your fake post.
	 * @var string
	 */
	var $page_title = 'Applypage';

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
	var $jobApi;
	var $toolApi;
	var $competenceApi;

	/**
	 * When initialized this will be the handled job
	 *
	 * @var mixed
	 */
	var $job;
	var $competences;
	var $jobId = 0;

	/**
	 * @param emol_captcha
	 */
	var $captcha;

	/**
	 * Class constructor
	 */
	function __construct( $slug, $function = '' ) {

		global $trailingData;

		$this->page_slug = $slug . '/' . $function;

		$this->emol_function = $function;

		//first connect to the api
		$this->emolApi = eazymatch_connect();

		if ( ! $this->emolApi ) {
			eazymatch_trow_error();
		}

		//split up the variables given
		$urlVars = explode( '/', $this->page_slug );
		$jobId   = $urlVars[1];

		//get competences
		//$this->competenceApi    = $this->emolApi->get('competence');
		//$this->competences         = $this->competenceApi->tree();
		$this->toolApi = $this->emolApi->get( 'tool' );

		if ( is_numeric( $jobId ) && $jobId > 0 ) {

			//initialize wsdls
			$this->jobApi = $this->emolApi->get( 'job' );

			//get the job
			$this->job = $this->jobApi->getFullPublished( $jobId );

			if ( empty( $this->job ) ) {

				header( "HTTP/1.0 404 Not Found" );
				header( 'Location: ' . get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_job_search_url' ) . $trailingData );
				exit();
			} else {
				$this->jobId = $this->job['id'];
			}


			//set the page variables    
			$this->page_title = EMOL_APPLY . ' "' . $this->job['name'] . '"';
		} else {

			$this->jobId      = 'open';
			$this->page_title = EMOL_JOB_APPLY_FREE;
		}
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
		 * The title of the page.
		 */
		$post->post_title = $this->page_title;

		/**
		 * This is the content of the post.  This is where the output of
		 * your plugin should go.  Just store the output from all your
		 * plugin function calls, and put the output into this var.
		 */

		$this->captcha = new emol_captcha();

		if ( emol_post_exists( 'EMOL_apply' ) ) {

			$secure = false;
			if ( emol_post_exists( 'emol_captcha_code' ) ) {
				if ( $this->captcha->isValid() || emol_session::isValidId( 'applicant_id' ) ) {
					$secure = true;
				}
			}

			if ( emol_post_exists( 'g-recaptcha-response' ) ) {

				$data_google = array(
					'secret'   => get_option( 'emol_frm_google_captcha_secret' ),
					'response' => emol_post( 'g-recaptcha-response' ),
					'remoteip' => $_SERVER['REMOTE_ADDR']
				);
				$options     = array(
					'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query( $data_google ),
					),
					"ssl"  => array(
						"verify_peer"      => false,
						"verify_peer_name" => false,
					)
				);
				$context     = stream_context_create( $options );

				$result = file_get_contents( 'https://www.google.com/recaptcha/api/siteverify', false, $context );

				$result = json_decode( $result, true );
				if ( $result['success'] == true ) {
					$secure = true;
				}
			}

			if ( $secure == true ) {
				$this->doApply();
			} else {
				$_POST['captcha-error'] = 1;
				$post->post_content     = $this->getContent( $_POST );
			}

		} else {

			$post->post_content = $this->getContent();

		}

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
	 * when someone has hit the button
	 */
	function doApply() {


		//captcha didnt check out....
		//initiate webservice method
		$ws = $this->emolApi->get( 'applicant' );

		//fetch birthdate parts
		$birthdate = null;
		$yeartest  = emol_post( 'birthdate-year' );
		if ( ! empty( $yeartest ) ) {
			$birthdate = emol_post( 'birthdate-year' ) . '-' . emol_post( 'birthdate-month' ) . '-' . emol_post( 'birthdate-day' );
		}

		if ( ! emol_session::isValidId( 'applicant_id' ) ) {

			//create a array the way EazyMatch likes it
			$subscription = new emol_ApplicantMutation();

			//set the person
			$subscription->setPerson(
				null,
				emol_post( 'firstname' ),
				emol_post( 'middlename' ),
				emol_post( 'lastname' ),
				$birthdate,
				emol_post( 'email' ),
				emol_post( 'password' ),
				emol_post( 'gender' ),
				emol_post( 'ssn' ),
				emol_post( 'nationality_id' ),
				emol_post( 'managercompany_id' )
			);


			//set the Applicant
			$subscription->setApplicant(
				null,
				date( 'Ymd' ),
				date( 'Ymd' ),
				null,
				emol_post( 'title' ),
				emol_post( 'healthcarereference' ),
				emol_post( 'linkedInrequest' ),
				emol_post( 'contactvia' ),
				emol_post( 'maritalstatus_id' ),
				emol_post( 'searchlocation' ),
				emol_post( 'salary' ),
				emol_post( 'availablehours' )
			);

			//set addresses
			if ( isset( $_POST['street'] ) && $_POST['street'] != '' ) {

				$subscription->addAddress(
					null,
					null,
					emol_post( 'country_id' ),
					null,
					emol_post( 'street' ),
					emol_post( 'housenumber' ),
					emol_post( 'extension' ),
					emol_post( 'zipcode' ),
					emol_post( 'city' )
				);

			} elseif ( isset( $_POST['zipcode'] ) && $_POST['zipcode'] != '' ) {

				$addrPiece = $this->toolApi->getAddressByZipcode( emol_post( 'zipcode' ) );

				$addrPiece['province_id'] = ( isset( $addrPiece['province_id'] ) ? $addrPiece['province_id'] : null );
				$addrPiece['country_id']  = ( isset( $addrPiece['country_id'] ) ? $addrPiece['country_id'] : null );
				$addrPiece['region_id']   = ( isset( $addrPiece['region_id'] ) ? $addrPiece['region_id'] : null );
				$addrPiece['street']      = ( isset( $addrPiece['street'] ) ? $addrPiece['street'] : null );
				$addrPiece['zipcode']     = ( isset( $addrPiece['zipcode'] ) ? $addrPiece['zipcode'] : null );
				$addrPiece['city']        = ( isset( $addrPiece['city'] ) ? $addrPiece['city'] : null );

				$subscription->addAddress(
					null,
					$addrPiece['province_id'],
					$addrPiece['country_id'],
					$addrPiece['region_id'],
					$addrPiece['street'],
					emol_post( 'housenumber' ),
					emol_post( 'extension' ),
					$addrPiece['zipcode'],
					$addrPiece['city']
				);

			}

			/**email**/
			$subscription->addEmailaddresses( null, null, emol_post( 'email' ) );
			/**phonenumber**/
			if ( get_option( 'emol_frm_app_phone' ) !== '' ) {
				$subscription->addPhonenumber( null, null, emol_post( 'phonenumber' ) );
			}

			if ( get_option( 'emol_frm_app_phone2' ) !== '' ) {
				$subscription->addPhonenumber( null, null, emol_post( 'phonenumber2' ) );
			}


			if ( get_option( 'emol_frm_app_schoolingtype_id' ) !== '' ) {
				$emol_frm_app_schoolingtype_id = emol_post( 'schoolingtype_id' );

				if ( is_numeric( $emol_frm_app_schoolingtype_id ) ) {
					$subscription->addSchooling( $emol_frm_app_schoolingtype_id );
				}
			}

			//CV
			if ( isset( $_FILES['cv'] ) && isset( $_FILES['cv']['tmp_name'] ) && $_FILES['cv']['tmp_name'] != '' ) {
				//set the CV document
				$doc            = array();
				$doc['name']    = $_FILES['cv']['name'];
				$doc['content'] = base64_encode( file_get_contents( $_FILES['cv']['tmp_name'] ) );
				$doc['type']    = $_FILES['cv']['type'];

				$subscription->setCV( $doc['name'], $doc['type'], $doc['content'] );
			}

			//photo
			if ( isset( $_FILES['picture'] ) && isset( $_FILES['picture']['tmp_name'] ) && $_FILES['picture']['tmp_name'] != '' ) {
				//set the CV document
				$doc            = array();
				$doc['name']    = $_FILES['picture']['name'];
				$doc['content'] = base64_encode( file_get_contents( $_FILES['picture']['tmp_name'] ) );
				$doc['type']    = $_FILES['picture']['type'];


				$subscription->setPicture( $doc['name'], $doc['type'], $doc['content'] );
			}

			//competences
			$competenceElements = get_option( 'emol_frm_app_competence', array() );
			foreach ( $competenceElements as $competence ) {
				if ( emol_post_exists( 'competence' . $competence['competence_id'] ) ) {
					foreach ( emol_post( 'competence' . $competence['competence_id'] ) as $cpt ) {
						$subscription->addCompetence( $cpt );
					}
				}
			}

			//job / mediation / match
			if ( emol_post( 'job_id' ) == '' ) {
				emol_post_set( 'job_id', null );
			}

			$url = $_SERVER['HTTP_HOST'];

			$contentMessage = nl2br( emol_post( 'motivation' ) );
			$contentMessage .= '<br /><br />(' . $url . ')';

			$subscription->setApplication(
				emol_post( 'job_id' ), $contentMessage, $url
			);

			//create the workable postable array
			$postData = $subscription->createSubscription();

			//option to directly go in the sys instead of webaanmeldingen
			$postData['processDirectly'] = get_option( 'emol_apply_process_directly' );

			//save the subscription to EazyMatch, this will send an notification to emol user and an email to the subscriber
			$ws->subscription( $postData );

			//naar eigen url of naar globale
			$redirectUrl = get_option( 'emol_apply_url_success_redirect' );
			if ( ! empty( $redirectUrl ) ) {
				wp_redirect( $redirectUrl );
				die();
			} else {
				wp_redirect( get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/' . $this->jobId . '/success/' );
				die();
			}
			exit;

		} else {
			/**
			 * apply to job, the true in the end is for triggering mail event
			 * EazyMatch will create a mediation between the job and applicant with the motivation.
			 * It also will register a correspondence moment and will send an e-mail to the emol user ( notification )
			 **/

			$success = $ws->applyToJob( emol_post( 'job_id' ), emol_session::get( 'applicant_id' ), nl2br( emol_post( 'motivation' ) ), true );

			if ( $success == true ) {

				wp_redirect( get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/' . $this->jobId . '/success/' );
				die();
			} else {

				wp_redirect( get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/' . $this->jobId . '/unsuccess/' );
				die();
			}
		}

	}


	/**
	 * creates the fake content
	 */
	function getContent( $defaultData = array() ) {

		// remove auto line breaks
		remove_filter( 'the_content', 'wpautop' );

		//fillup default array of presets
		$data['birthdate']  = '';
		$data['firstname']  = '';
		$data['lastname']   = '';
		$data['middlename'] = '';

		$data['city']             = '';
		$data['ssn']              = '';
		$data['nationality_id']   = '';
		$data['street']           = '';
		$data['country_id']       = '';
		$data['maritalstatus_id'] = '';

		$data['availablehours']    = '';
		$data['description']       = '';
		$data['title']             = '';
		$data['housenumber']       = '';
		$data['phonenumber']       = '';
		$data['phonenumber2']      = '';
		$data['managercompany_id'] = '';
		$data['schoolingtype_id']  = '';
		$data['zipcode']           = '';
		$data['email']             = '';
		$data['extension']         = '';
		$data['competence']        = array();

		//fill up default data
		if ( isset( $defaultData ) && count( $defaultData ) > 0 && isset( $defaultData['birthdate-year'] ) && isset( $defaultData['birthdate-month'] ) && isset( $defaultData['birthdate-day'] ) ) {
			$data              = $defaultData;
			$data['birthdate'] = $defaultData['birthdate-year'] . '-' . $defaultData['birthdate-month'] . '-' . $defaultData['birthdate-day'];
		}

		// prepare client resources
		// emol_require::validation();
		// emol_require::jqueryUi();

		if ( isset( $data['linkedInrequest'] ) ) {
			$linkedInrequest = $data['linkedInrequest'];
		} else {
			$linkedInrequest = ( get_query_var( 'emolrequestid' ) );
		}

		$api = $this->emolApi;

		$inImage = 'connect-to-linkedin.png';
		if ( strlen( $linkedInrequest ) == 128 ) {

			$appApi = $this->emolApi->get( 'applicant' );
			$dataIn = $appApi->getLinkedInProfile( $linkedInrequest );


			if ( ! empty( $dataIn['date-of-birth'] ) ) {
				$data['birthdate'] = $dataIn['date-of-birth']['year'] . '-' . $dataIn['date-of-birth']['month'] . '-' . $dataIn['date-of-birth']['day'];
			}

			//normalize data
			$data['title']       = @$dataIn['headline'];
			$data['email']       = @$dataIn['email-address'];
			$data['phonenumber'] = @$dataIn['phone-numbers']['phone-number']['phone-number'];
			$data['description'] = @$dataIn['summary'];
			$data['city']        = @$dataIn['location']['name'];
			$dataIn['last-name'] = explode( ' ', $dataIn['last-name'] );
			$data['lastname']    = array_pop( $dataIn['last-name'] );
			$data['firstname']   = $dataIn['first-name'];
			$data['middlename']  = implode( ' ', $dataIn['last-name'] );
			//$data['birthdate'] = $dataIn['birthdate-year'].'-'.$dataIn['birthdate-month'].'-'.$dataIn['birthdate-day'];
			$inImage = 'connected-to-linkedin.png';
		}

		$firstDescription = '';
		if ( isset( $this->job['description'] ) && trim( $this->job['description'] != '' ) ) {
			$firstDescription = '<p id="emol-apply-job-summary">' . EMOL_APPLY_HEADER . ' <strong>' . $this->job['name'] . '</strong>.</p>';
		}

		//the apply form
		include( EMOL_DIR . '/lib/emol/loginwidget.php' );

		$mailto = get_option( 'emol_email' );

		$applyHtml = $firstDescription . $loginWidget . '
        <div id="emol-form-apply" class="emol-form-div emol-form-table">
        <form method="post" id="emol-apply-form" enctype="multipart/form-data">
        <input type="hidden" name="job_id" value="' . $this->jobId . '" />
        <input type="hidden" name="EMOL_apply" value="1" />
        <input type="hidden" name="linkedInrequest" value="' . $linkedInrequest . '" />';

		$urlVars = explode( '/', $this->page_slug );

		//url applying
		$url = ( ! empty( $_SERVER['HTTPS'] ) ) ? "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		//$url = $url . '/';

		if ( isset( $urlVars[2] ) && $urlVars[2] == 'success' ) {
			$applyHtml .= '<tr><td colspan="2">' . stripslashes( get_option( 'emol_apply_success' ) ) . '</td></tr>';
		} elseif ( isset( $urlVars[2] ) && $urlVars[2] == 'unsuccess' ) {
			$applyHtml .= '<tr><td colspan="2">' . EMOL_APPLY_FAIL_MSG . '</td></tr>';
		} else {
			$applyHtml .= '<tr><td colspan="2" class="emol-apply-mandatory">' . EMOL_APPLY_MANDATORY . '</td></tr>';
			include( EMOL_DIR . '/lib/emol/applyform.php' );
		}

		//finish up html
		$applyHtml .= '
        </div>
        <script type="text/javascript">
        /**
        * EazyMatch validation
        */
        jQuery(document).ready(function(){
            jQuery.validator.messages.required = "' . EMOL_ERR_REQUIRED . '";
            jQuery("#emol-apply-form").validate({
                messages: {
                    zipcode: { 
                        minlength: "' . EMOL_ERR_MIN_CHAR_ZIPCODE . '"
                    },
                    email: { email: "' . EMOL_ERR_VALID_EMAIL . '" }
                },
                submitHandler: function(form) {
                     jQuery(\'#emol-apply-submit-button\').attr(\'disabled\',\'disabled\');
                     jQuery(\'#emol-apply-submit-button\').attr(\'value\',\'Een moment geduld a.u.b...\');
                     form.submit();
                }
            });
        });
        </script>';

		//return some html
		return str_replace( PHP_EOL, ' ', $applyHtml );
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
