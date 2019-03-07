<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * inschrijfformulier... needs some improvements, handeling of this form is now done by the fakepost script
 *
 */
class emol_shortcode_apply {

	var $captcha;

	/**
	 * determines what will happen
	 *
	 */
	function getContent() {

		if ( isset( $_POST['birthdate-year'] ) && isset( $_POST['birthdate-month'] ) && isset( $_POST['birthdate-day'] ) ) {
			//fetch birthdate parts
			$_POST['birthdate'] = $_POST['birthdate-year'] . '-' . $_POST['birthdate-month'] . '-' . $_POST['birthdate-day'];
		}

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
				$result      = file_get_contents( 'https://www.google.com/recaptcha/api/siteverify', false, $context );

				$result = json_decode( $result, true );
				if ( $result['success'] == true ) {
					$secure = true;
				}
			}

			if ( $secure == true ) {
				return $this->doApply();
			} else {
				$_POST['captcha-error'] = 1;

				return $this->createForm();
			}

		} else {

			return $this->createForm();

		}

	}


	/**
	 * creates a subscription in EazyMatch
	 *
	 */
	function doApply() {

		$api = eazymatch_connect();
		$ws  = $api->get( 'applicant' );

		//fetch birthdate parts
		$birthdate = emol_post( 'birthdate-year' ) . '-' . emol_post( 'birthdate-month' ) . '-' . emol_post( 'birthdate-day' );


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

			//save the subscription to EazyMatch, this will send an notification to emol user and an email to the subscriber
			$ws->subscription( $postData );

			$_POST['success'] = 1;
			//wp_redirect( get_bloginfo('wpurl').'/'.get_option('emol_apply_url').'/'.$this->jobId .'/success/' );

		} else {
			/**
			 * apply to job, the true in the end is for triggering mail event
			 * EazyMatch will create a mediation between the job and applicant with the motivation.
			 * It also will register a correspondence moment and will send an e-mail to the emol user ( notification )
			 **/

			$success = $ws->applyToJob( emol_post( 'job_id' ), emol_session::get( 'applicant_id' ), nl2br( emol_post( 'motivation' ) ), true );

			if ( $success == true ) {
				$_POST['success'] = 1;

			} else {
				$_POST['success'] = - 1;

			}

		}


		$applyHtml = $this->createForm();


		return ( $applyHtml );
	}

	/**
	 * gets the default form
	 *
	 */
	function createForm() {


		//fillup default array of presets
		$data['birthdate']         = '';
		$data['firstname']         = '';
		$data['lastname']          = '';
		$data['middlename']        = '';
		$data['city']              = '';
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


		global $trailingData;
		global $emol_side;

		$emol_side = 'applicant';

		$api = eazymatch_connect();

		//wrong captcha?
		$data = ( isset( $_POST['captcha-error'] ) && $_POST['captcha-error'] == 1 ) ? $_POST : $data;

		if ( isset( $data['linkedInrequest'] ) ) {
			$linkedInrequest = $data['linkedInrequest'];
		} else {
			$linkedInrequest = ( get_query_var( 'emolrequestid' ) );
		}

		$inImage = 'connect-to-linkedin.png';
		if ( strlen( $linkedInrequest ) == 128 ) {

			$appApi = $api->get( 'applicant' );
			$dataIn = $appApi->getLinkedInProfile( $linkedInrequest );

			//normalize data
			$data['title']       = $dataIn['headline'];
			$data['phonenumber'] = @$dataIn['phone-numbers']['phone-number']['phone-number'];
			$data['description'] = $dataIn['summary'];
			$data['city']        = $dataIn['location']['name'];
			$dataIn['last-name'] = explode( ' ', $dataIn['last-name'] );
			$data['lastname']    = array_pop( $dataIn['last-name'] );
			$data['firstname']   = $dataIn['first-name'];
			$data['middlename']  = implode( ' ', $dataIn['last-name'] );
			$data['birthdate']   = $dataIn['date-of-birth']['year'] . '-' . $dataIn['date-of-birth']['month'] . '-' . $dataIn['date-of-birth']['day'];
			$inImage             = 'connected-to-linkedin.png';
		}

		$firstDescription = '';

		//the apply form
		include( EMOL_DIR . '/lib/emol/loginwidget.php' );


		$mailto = get_option( 'emol_email' );

		$apply_url = get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/0/open';

		$applyHtml = $firstDescription . $loginWidget . '
        <div id="emol-form-apply" class="emol-form-div">
        <form method="post" action="' . $apply_url . '" id="emol-apply-form" enctype="multipart/form-data">
        <input type="hidden" name="job_id" value="open" />
        <input type="hidden" name="EMOL_apply" value="1" />
        <input type="hidden" name="linkedInrequest" value="' . $linkedInrequest . '" />
        <table class="emol-form-table">
        <tbody>';


		//url applying
		$url = ( ! empty( $_SERVER['HTTPS'] ) ) ? "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		//$url = $url . '/';

		if ( isset( $_POST['success'] ) && $_POST['success'] == 1 ) {
			$applyHtml .= '<tr><td colspan="2">' . nl2br( get_option( 'emol_apply_success' ) ) . '</td></tr>';
		} elseif ( isset( $_POST['success'] ) && $_POST['success'] == - 1 ) {
			$applyHtml .= '<tr><td colspan="2">' . EMOL_APPLY_FAIL_MSG . '</td></tr>';
		} else {
			$applyHtml .= '<tr><td colspan="2" class="emol-apply-mandatory">' . EMOL_APPLY_MANDATORY . '</td></tr>';
			include( EMOL_DIR . '/lib/emol/applyform.php' );
		}


		//finish up html
		$applyHtml .= '</tbody>
        </table>
        </form>
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
		return $applyHtml;


	}
}

