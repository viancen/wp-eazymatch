<?php

class emol_forminstance_applicant_account extends emol_form_instance {
	// available fields
	protected $availableFields = array(
		'password'             => array(
			'type'    => 'emol_form_field_text',
			'label'   => EMOL_ACCOUNT_APP_PASSWORD,
			'mapping' => 'Person.password'
		),
		'username'             => array(
			'type'    => 'emol_form_field_text',
			'label'   => EMOL_ACCOUNT_APP_USERNAME,
			'mapping' => 'Person.username'
		),
		'birthday'             => array(
			'type'    => 'emol_form_field_date',
			'label'   => EMOL_BIRTHDATE,
			'mapping' => 'Person.birthdate'
		),
		'available'            => array(
			'type'    => 'emol_form_field_combo_yesno',
			'label'   => EMOL_ACCOUNT_APP_AVAILABLE,
			'mapping' => 'available'
		),
		'availablefrom'        => array(
			'type'    => 'emol_form_field_date',
			'label'   => EMOL_ACCOUNT_APP_AVAILABLE_FROM,
			'mapping' => 'availablefrom'
		),
		'availableto'          => array(
			'type'    => 'emol_form_field_date',
			'label'   => EMOL_ACCOUNT_APP_AVAILABLE_TO,
			'mapping' => 'availableto'
		),
		'gender'               => array(
			'type'    => 'emol_form_field_combo_gender',
			'label'   => EMOL_ACCOUNT_GENDER,
			'mapping' => 'Person.gender'
		),
		'firstname'            => array(
			'type'       => 'emol_form_field_text',
			'label'      => EMOL_ACCOUNT_APP_FIRSTNAME,
			'mapping'    => 'Person.firstname',
			'allowBlank' => false
		),
		'middlename'           => array(
			'type'    => 'emol_form_field_text',
			'label'   => EMOL_ACCOUNT_APP_MIDDLENAME,
			'mapping' => 'Person.middlename'
		),
		'lastname'             => array(
			'type'    => 'emol_form_field_text',
			'label'   => EMOL_ACCOUNT_APP_LASTNAME,
			'mapping' => 'Person.lastname'
		),
		'combinedlastname'     => array(
			'type'  => 'emol_form_field_eazy_combinedlastname',
			'label' => EMOL_ACCOUNT_APP_COMBINEDLASTNAME
		),
		'title'                => array(
			'type'    => 'emol_form_field_text',
			'label'   => EMOL_ACCOUNT_APP_FUNCTION,
			'mapping' => 'title'
		),
		'shortcode'            => array(
			'type'    => 'emol_form_field_text',
			'label'   => EMOL_ACCOUNT_APP_SHORTCODE,
			'mapping' => 'Person.shortcode'
		),
		'picture'              => array(
			'type'  => 'emol_form_field_picture',
			'label' => EMOL_ACCOUNT_APP_PHOTO
		),
		'cv'                   => array(
			'type'  => 'emol_form_field_file',
			'label' => EMOL_ACCOUNT_APP_CV
		),
		'address'              => array(
			'type'  => 'emol_form_field_eazy_address',
			'label' => EMOL_ACCOUNT_APP_ADDRESS
		),
		'phonenumber'          => array(
			'type'  => 'emol_form_field_eazy_phonenumber',
			'label' => EMOL_ACCOUNT_APP_PHONE
		),
		'emailaddress'         => array(
			'type'  => 'emol_form_field_eazy_emailaddress',
			'label' => EMOL_ACCOUNT_APP_EMAIL
		),
		'skill'                => array(
			'type'    => 'emol_form_grid_skill',
			'label'   => EMOL_ACCOUNT_APP_SKILL_LABEL,
			'mapping' => 'Skill'
		),
		'schooling'            => array(
			'type'    => 'emol_form_grid_schooling',
			'label'   => EMOL_ACCOUNT_APP_SCHOOLING_LABEL,
			'mapping' => 'Schooling'
		),
		'experience'           => array(
			'type'    => 'emol_form_grid_experience',
			'label'   => EMOL_ACCOUNT_APP_EXPERIENCE_LABEL,
			'mapping' => 'Experience'
		),
		'identification'       => array(
			'type'    => 'emol_form_grid_identification',
			'label'   => EMOL_ACCOUNT_APP_IDENTIFICATION_LABEL,
			'mapping' => 'Identificationfilestore'
		),
		'competence'           => array(
			'type'  => 'emol_form_field_checktree_competence',
			'label' => 'competence'
		),
		'applicantphase'       => array(
			'type'  => 'emol_form_field_checktree_applicantphase',
			'label' => 'applicantphase'
		),
		'applicantstatus'      => array(
			'type'  => 'emol_form_field_checktree_applicantstatus',
			'label' => 'applicantstatus'
		),
		'applicantvaluestatus' => array(
			'type'  => 'emol_form_field_checktree_applicantvaluestatus',
			'label' => 'applicantvaluestatus'
		)
	);


	public function initData() {
		$emol = emol_connectManager::getInstance()->getConnection();

		// create a new trunk request
		$trunk = new emol_trunk();

		$applicant        = &$trunk->request( 'applicant', 'getPersonalPrivate' );
		$applicantProfile = &$trunk->request( 'applicant', 'getProfilePrivate' );

		// execute the trunk request
		$trunk->execute();

		if ( $this->getField( 'combinedlastname' ) !== false ) {
			$this->getField( 'combinedlastname' )->setValue( array(
				'middlename' => $applicant['Person']['middlename'],
				'lastname'   => $applicant['Person']['lastname']
			) );
		}


		if ( $this->getField( 'address' ) !== false ) {
			$addresses = $applicant['Person']['Addresses'];
			if ( is_array( $addresses ) && count( $addresses ) > 0 ) {
				$preferedAddress = false;
				$preferedid      = $applicant['Person']['preferedaddress_id'];

				foreach ( $addresses as $address ) {
					if ( $address['id'] == $preferedid ) {
						$preferedAddress = $address;
						break;
					}
				}

				if ( $preferedAddress == false ) {
					$preferedAddress = $addresses[0];
				}

				$this->getField( 'address' )->setValue( $preferedAddress );
			}
		}

		if ( $this->getField( 'phonenumber' ) !== false ) {
			$phonenumbers = $applicant['Person']['Phonenumbers'];
			if ( is_array( $phonenumbers ) && count( $phonenumbers ) > 0 ) {
				$preferedPhonenumber = false;
				$preferedid          = $applicant['Person']['preferedphonenumber_id'];

				foreach ( $phonenumbers as $phonenumber ) {
					if ( $phonenumber['id'] == $preferedid ) {
						$preferedPhonenumber = $phonenumber;
						break;
					}
				}

				if ( $preferedPhonenumber == false ) {
					$preferedPhonenumber = $phonenumbers[0];
				}

				$this->getField( 'phonenumber' )->setValue( $preferedPhonenumber );
			}
		}

		if ( $this->getField( 'emailaddress' ) !== false ) {
			$emailaddresses = $applicant['Person']['Emailaddresses'];
			if ( is_array( $emailaddresses ) && count( $emailaddresses ) > 0 ) {
				$preferedEmailaddress = false;
				$preferedid           = $applicant['Person']['preferedemailaddress_id'];

				foreach ( $emailaddresses as $emailaddress ) {
					if ( $emailaddress['id'] == $preferedid ) {
						$preferedEmailaddress = $emailaddress;
						break;
					}
				}

				if ( $preferedEmailaddress == false ) {
					$preferedEmailaddress = $emailaddresses[0];
				}

				$this->getField( 'emailaddress' )->setValue( $preferedEmailaddress );
			}
		}

		if ( $this->getField( 'cv' ) !== false ) {
			$this->getField( 'cv' )->setValue( $emol->applicant->downloadCV() );
		}

		if ( $this->getField( 'picture' ) !== false ) {
			$this->getField( 'picture' )->setValue( $emol->person->getPicture( $applicant['person_id'] ) );
		}

		/**
		 * check for competence edit fields
		 */
		$competenceFields = $this->getFieldsByType( 'emol_form_field_checktree_competence' );

		if ( count( $competenceFields ) > 0 ) {
			$competenceIds = array();
			foreach ( $applicantProfile['Competence'] as $competence ) {
				$competenceIds[] = $competence['id'];
			}

			foreach ( $competenceFields as $competenceField ) {
				$competenceField->setValue( $competenceIds );
			}
		}

		/**
		 * check for applicant status edit fields
		 */
		$statusFields = $this->getFieldsByType( 'emol_form_field_checktree_applicantstatus' );

		if ( count( $statusFields ) > 0 ) {
			$statusIds = array();
			foreach ( $applicant['Statusses'] as $status ) {
				$statusIds[] = $status['applicantstatus_id'];
			}

			foreach ( $statusFields as $statusField ) {
				$statusField->setValue( $statusIds );
			}
		}

		/**
		 * check for applicant valuestatus edit fields
		 */
		$valuestatusFields = $this->getFieldsByType( 'emol_form_field_checktree_applicantvaluestatus' );

		if ( count( $valuestatusFields ) > 0 ) {
			$valuestatusIds = array();
			foreach ( $applicant['Valuestatusses'] as $valuestatus ) {
				$valuestatusIds[] = $valuestatus['applicantvaluestatus_id'];
			}

			foreach ( $valuestatusFields as $valuestatusField ) {
				$valuestatusField->setValue( $valuestatusIds );
			}
		}


		/**
		 * check for applicant phase edit fields
		 */
		$phaseFields = $this->getFieldsByType( 'emol_form_field_checktree_applicantphase' );

		if ( count( $phaseFields ) > 0 ) {
			$pahaseIds = array();
			foreach ( $applicant['Phases'] as $phase ) {
				$pahaseIds[] = $phase['applicantphase_id'];
			}

			foreach ( $phaseFields as $phaseField ) {
				$phaseField->setValue( $pahaseIds );
			}
		}

		$this->mapData( $applicant );
		$this->mapData( $applicantProfile );
	}

	public function persist() {
		$emol = emol_connectManager::getInstance()->getConnection();


		$trunk = new emol_trunk();

		$competenceFields = $this->getFieldsByType( 'emol_form_field_checktree_competence' );
		if ( count( $competenceFields ) > 0 ) {
			foreach ( $competenceFields as $competenceField ) {
				$trunk->request( 'applicant', 'editCompetencePrivate', array(
					$competenceField->getValue(),
					$competenceField->getConfig( 'treeroot_id' )
				) );
			}
		}

		$statusFields = $this->getFieldsByType( 'emol_form_field_checktree_applicantstatus' );
		if ( count( $statusFields ) > 0 ) {
			foreach ( $statusFields as $statusField ) {
				$trunk->request( 'applicant', 'editStatusPrivate', array(
					$statusField->getValue(),
					$statusField->getConfig( 'treeroot_id' )
				) );
			}
		}

		$valuestatusFields = $this->getFieldsByType( 'emol_form_field_checktree_applicantvaluestatus' );
		if ( count( $valuestatusFields ) > 0 ) {
			foreach ( $valuestatusFields as $valuestatusField ) {
				$trunk->request( 'applicant', 'editValuestatusPrivate', array(
					$valuestatusField->getValue(),
					$valuestatusField->getConfig( 'treeroot_id' )
				) );
			}
		}

		$phaseFields = $this->getFieldsByType( 'emol_form_field_checktree_applicantphase' );
		if ( count( $phaseFields ) > 0 ) {
			foreach ( $phaseFields as $phaseField ) {
				$trunk->request( 'applicant', 'editPhasePrivate', array(
					$phaseField->getValue(),
					$phaseField->getConfig( 'treeroot_id' )
				) );
			}
		}

		$picture = $this->getField( 'picture' );
		if ( $picture !== false ) {
			$postValue = $picture->getPostValue();

			if ( $postValue !== false ) {
				$trunk->request( 'person', 'setPicturePrivate', array( $postValue['content'] ) );
			}

		}

		$cv = $this->getField( 'cv' );
		if ( $cv !== false ) {
			$postValue = $cv->getPostValue();

			if ( $postValue !== false ) {
				$trunk->request( 'applicant', 'createCurriculumDocumentPrivate', array( $postValue ) );
			}
		}

		// collect person information
		$applicantData = $this->getMappedValues();


		$combinedlastname = $this->getField( 'combinedlastname' );
		if ( $combinedlastname !== false ) {
			if ( ! is_array( $applicantData['Person'] ) ) {
				$applicantData['Person'] = array();
			}

			$applicantData['Person'] = array_merge( $applicantData['Person'], $combinedlastname->getValue() );
		}

		// get special fields
		$address      = $this->getField( 'address' );
		$emailaddress = $this->getField( 'emailaddress' );
		$phonenumber  = $this->getField( 'phonenumber' );

		if ( $address !== false || $emailaddress !== false || $phonenumber !== false ) {
			$applicant = $emol->applicant->getPersonalPrivate();

			if ( $address !== false ) {
				$addressValue = $address->getValue();

				$addresses    = $applicant['Person']['Addresses'];
				$addressIndex = false;
				$indexCounter = - 1;

				if ( is_array( $addresses ) && count( $addresses ) > 0 ) {
					$indexCounter = - 1;
					foreach ( $addresses as $addressRow ) {
						$indexCounter ++;
						if ( $addressRow['id'] == $addressValue['id'] ) {
							$addressIndex = $indexCounter;
							break;
						}
					}
				}

				// set the preferedaddress_id if none set
				if ( ! is_numeric( $applicant['Person']['preferedaddress_id'] ) ) {
					if ( ! is_numeric( $addressValue['id'] ) ) {
						$addressValue['id'] = '-1';
					}

					$applicantData['Person']['preferedaddress_id'] = $addressValue['id'];
				}

				if ( $addressIndex !== false ) {
					$addresses[ $addressIndex ] = $addressValue;
				} else {
					$addresses[] = $addressValue;
				}

				$applicantData['Person']['Addresses'] = $addresses;
			}

			if ( $phonenumber !== false ) {
				$phonenumberValue = $phonenumber->getValue();

				$phonenumbers     = $applicant['Person']['Phonenumbers'];
				$phonenumberIndex = false;
				$indexCounter     = - 1;

				if ( is_array( $phonenumbers ) && count( $phonenumbers ) > 0 ) {
					$indexCounter = - 1;
					foreach ( $phonenumbers as $phonenumberRow ) {
						$indexCounter ++;
						if ( $phonenumberRow['id'] == $phonenumberValue['id'] ) {
							$phonenumberIndex = $indexCounter;
							break;
						}
					}
				}

				// set the preferedphonenumber_id if none set
				if ( ! is_numeric( $applicant['Person']['preferedphonenumber_id'] ) ) {
					if ( ! is_numeric( $phonenumberValue['id'] ) ) {
						$phonenumberValue['id'] = '-1';
					}

					$applicantData['Person']['preferedphonenumber_id'] = $phonenumberValue['id'];
				}

				if ( $phonenumberIndex !== false ) {
					$phonenumbers[ $phonenumberIndex ] = $phonenumberValue;
				} else {
					$phonenumbers[] = $phonenumberValue;
				}

				$applicantData['Person']['Phonenumbers'] = $phonenumbers;
			}


			if ( $emailaddress !== false ) {
				$emailaddressValue = $emailaddress->getValue();

				$emailaddresses    = $applicant['Person']['Emailaddresses'];
				$emailaddressIndex = false;
				$indexCounter      = - 1;

				if ( is_array( $emailaddresses ) && count( $emailaddresses ) > 0 ) {
					$indexCounter = - 1;
					foreach ( $emailaddresses as $emailaddressRow ) {
						$indexCounter ++;
						if ( $emailaddressRow['id'] == $emailaddressValue['id'] ) {
							$emailaddressIndex = $indexCounter;
							break;
						}
					}
				}

				// set the preferedemailaddress_id if none set
				if ( ! is_numeric( $applicant['Person']['preferedemailaddress_id'] ) ) {
					if ( ! is_numeric( $emailaddressValue['id'] ) ) {
						$emailaddressValue['id'] = '-1';
					}

					$applicantData['Person']['preferedemailaddress_id'] = $emailaddressValue['id'];
				}

				if ( $emailaddressIndex !== false ) {
					$emailaddresses[ $emailaddressIndex ] = $emailaddressValue;
				} else {
					$emailaddresses[] = $emailaddressValue;
				}

				$applicantData['Person']['Emailaddresses'] = $emailaddresses;
			}

		}

		if ( isset( $applicantData['Identificationfilestore'] ) ) {
			$applicantData['Person']['Identifications'] = $applicantData['Identificationfilestore'];
			unset( $applicantData['Identificationfilestore'] );
		}

		// apply simple mapped values
		$trunk->request( 'applicant', 'editProfilePrivate', array( $applicantData ) );
		$trunk->execute();
	}
}