<?php

/**
 * Container for job view
 */
class emol_page_applicant_account_edit extends emol_page {
	protected function preparePost() {
		switch ( $this->request_vars ) {
			case 'edit' :
				return $this->prepareEdit();
				break;

			case 'identificationdoc' :
				return $this->downloadIdentification();
				break;
		}
	}

	function getContent() {
		switch ( $this->request_vars ) {
			case 'login' :
				return $this->doLogin();
				break;

			case 'logout' :
				return $this->doLogout();
				break;


			case 'edit' :
				return $this->doEdit();
				break;
		}
	}

	private $editForm;

	function prepareEdit() {

		// get the form to use\
		$formManager = emol_form_manager::getInstance();

		$listIds = $formManager->getListIds( 'emol_forminstance_applicant_account' );

		if ( count( $listIds ) == 0 ) {
			// TODO: remove this when admind page is ready, add 404
			$this->tempFormConfig();
			$listIds = $formManager->getListIds( 'emol_forminstance_applicant_account' );
		}

		if ( isset( $_GET['f'] ) && in_array( $_GET['f'], $listIds ) ) {
			$listId         = $_GET['f'];
			$this->editForm = $formManager->get( $listId );
		} else {
			$listId         = $listIds[0];
			$this->editForm = $formManager->get( $listId );
		}


		// TODO: get page title form form
		$this->page_title = "Profiel bewerken";
	}

	function doEdit() {
		$form = $this->editForm;

		$form->initData();

		if ( $form->checkPOST() ) {
			if ( $form->validate() ) {
				// process response
				$form->persist();

				$f = isset( $_GET['f'] ) ? '?f=' . $_GET['f'] : '';

				wp_redirect( '/' . get_option( 'emol_account_url' ) . '/edit/' . $f );
				exit();
			}
		}


		return $this->loadView( 'applicant/account/edit.php', array( 'form' => $form ) );
	}

	function tempFormConfig() {
		// get the form to use\
		$formManager = emol_form_manager::getInstance();

		// create naw form
		$form = $formManager->create( 'emol_forminstance_applicant_account' );

		$form->setFieldConfig( array(
			array(
				'id' => 'gender',
			),
			array(
				'id' => 'birthday'
			),
			array(
				'id'         => 'firstname',
				'allowBlank' => false
			),
			array(
				'id' => 'combinedlastname'
			),
			array(
				'id' => 'phonenumber'
			),
			array(
				'id' => 'emailaddress'
			),
			array(
				'id' => 'address'
			),
			array(
				'id' => 'available'
			),
			array(
				'id' => 'availablefrom'
			),
			array(
				'id' => 'availableto'
			),
			array(
				'id' => 'title'
			),
			array(
				'id' => 'picture'
			),
			array(
				'id' => 'cv'
			)
		) );

		$formManager->persistInstanceConfig( $form );

		// create test naw form
		$form = $formManager->create( 'emol_forminstance_applicant_account' );

		$form->setFieldConfig( array(
			array(
				'id'    => 'skill',
				'label' => 'Cursussen en kennisgebieden'
			)
		) );

		$formManager->persistInstanceConfig( $form );


		// create test naw form
		$form = $formManager->create( 'emol_forminstance_applicant_account' );

		$form->setFieldConfig( array(
			array(
				'id' => 'schooling'
			)
		) );

		$formManager->persistInstanceConfig( $form );


		// create test naw form
		$form = $formManager->create( 'emol_forminstance_applicant_account' );

		$form->setFieldConfig( array(
			array(
				'id'    => 'experience',
				'label' => 'Relevante werkervaring en nevenactiviteiten'
			)
		) );

		$formManager->persistInstanceConfig( $form );


		// create test naw form
		$form = $formManager->create( 'emol_forminstance_applicant_account' );

		$form->setFieldConfig( array(
			array(
				'id'    => 'identification',
				'label' => 'Mijn documenten'
			)
		) );

		$formManager->persistInstanceConfig( $form );


		// create test competence form
		$form = $formManager->create( 'emol_forminstance_applicant_account' );

		$form->setFieldConfig( array(
			array(
				'id' => 'applicantphase'
			),
			array(
				'id'          => 'applicantstatus',
				'treeroot_id' => 4
			),
			array(
				'id' => 'applicantvaluestatus'
			),
			array(
				'id'          => 'competence',
				'treeroot_id' => 2
			),
			array(
				'id'          => 'competence',
				'treeroot_id' => 133
			)
		) );

		$formManager->persistInstanceConfig( $form );
	}

	/**
	 * when someone has hit the button to login
	 */
	function doLogin() {
		$wsLogin  = $this->emolApi->get( 'session' );
		$userPass = emol_post( 'password' );
		$userName = emol_post( 'username' );

		$userKey = $wsLogin->getUserToken( $userName, $userPass, null );

		if ( $userKey != '' ) {
			$connectionManager = emol_connectManager::getInstance();
			$connectionManager->setUserToken( $userKey );

			//reconnect with new credentials
			$this->emolApi = eazymatch_connect();

			//get user info
			$user = $this->emolApi->get( 'person' );
			$user = $user->getCurrent();

			emol_session::set( array(
				'applicant_id' => $user['isApplicant'],
				'company_id'   => $user['isCompany'],
				'contact_id'   => $user['isContact'],
				'person_id'    => $user['id']
			) );

			if ( emol_post( 'emol_redirect_url' ) ) {
				wp_redirect( emol_post( 'emol_redirect_url' ) );
			} else {


				if ( $user['isApplicant'] > 0 ) {
					wp_redirect( '/' . get_option( 'emol_account_url' ) . '/edit/' );
				} else {
					wp_redirect( '/' . get_option( 'emol_company_account_url' ) . '/edit/' );
				}

			}
			exit;

		} else {
			emol_session::terminate();

			return '<p>Uw logingegevens zijn niet juist...</p>';
		}

	}

	function downloadIdentification() {
		$documentId = isset( $_GET['id'] ) ? $_GET['id'] : false;

		if ( $documentId === false ) {
			exit( 'no id given' );
		}

		// create a new trunk request
		$trunk = new emol_trunk();

		$applicant = &$trunk->request( 'applicant', 'getPersonalPrivate' );

		// execute the trunk request
		$trunk->execute();

		foreach ( $applicant['Identificationfilestore'] as $identification ) {
			if ( isset( $identification['Document'] ) && $identification['Document']['id'] == $documentId ) {
				emol_document::download( $identification['Document'] );
			}
		}

		exit( 'document id invalid' );
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
		$logoutUrl = get_option( 'emol_logout_url' );

		if ( empty( $logoutUrl ) ) {
			$logoutUrl = '/';
		}

		header( 'location: ' . $logoutUrl );
		exit();
	}
}
