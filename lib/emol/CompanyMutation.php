<?php

/**
 * Structure of an company to be processed by EMOL3
 *
 * @author Vincent van den Nieuwenhuizen
 */
class emol_CompanyMutation {

	public $Company;

	//person arrays
	public $Addresses = array();
	public $Emailaddresses = array();
	public $Phonenumbers = array();

	//profile arrays
	public $Onlineprofiles = array();
	public $Competences = array();

	//container variables
	public $Contact;
	public $Person;
	public $Job;

	//documents
	public $Documents;

	//the application
	public $Application;

	public $Total;


	/**
	 * set company
	 *
	 * @param mixed $companyId
	 * @param mixed $name
	 * @param mixed $profile
	 * @param mixed $coc
	 */
	function setCompany(
		$companyId = null,
		$name = null,
		$profile = null,
		$companysize_id = null,
		$branche_id = null,
		$coc = null
	) {
		$this->Company = array(
			'id'             => $companyId,
			'name'           => $name,
			'profile'        => $profile,
			'companysize_id' => $companysize_id,
			'branche_id'     => $branche_id,
			'coc'            => $coc
		);
	}

	/**
	 * Person Object
	 *
	 * @param mixed $personId
	 * @param mixed $firstname
	 * @param mixed $lastname
	 * @param mixed $middlename
	 * @param mixed $birthdate
	 * @param mixed $gender
	 */
	function setPerson( $personId = null, $firstname = null, $middlename = null, $lastname = null, $birthdate = null, $username = null, $password = null, $gender = null ) {
		$this->Person = array(
			'id'         => $personId,
			'firstname'  => $firstname,
			'lastname'   => $lastname,
			'middlename' => $middlename,
			'birthdate'  => date( 'Ymd', strtotime( $birthdate ) ),
			'username'   => $username,
			'password'   => str_replace( '**********', '', $password ),
			'gender'     => $gender,
		);

	}

	/**
	 * set contact ojnbet
	 *
	 * @param mixed $contactId
	 * @param mixed $department
	 */
	function setContact( $contactId = null, $department = null ) {
		$this->Contact = array(
			'id'         => $contactId,
			'department' => $department,
		);
	}

	/**
	 * Add addresses
	 *
	 * @param mixed $province_id
	 * @param mixed $country_id
	 * @param mixed $region_id
	 * @param mixed $street
	 * @param mixed $housenumber
	 * @param mixed $extension
	 * @param mixed $zipcode
	 * @param mixed $city
	 */
	function addAddress(
		$id = null,
		$province_id = null, $country_id = null, $region_id = null, $street = null, $housenumber = null, $extension = null, $zipcode = null, $city = null, $addresstype_id = null
	) {

		if ( $id === null ) {
			$id = rand( - 1, - 99999 );
		}
		$this->Addresses[] = array(
			'id'             => $id,//to relate to preferred address
			'addresstype_id' => $addresstype_id,
			'province_id'    => $province_id,
			'country_id'     => $country_id,
			'region_id'      => $region_id,
			'street'         => $street,
			'housenumber'    => $housenumber,
			'extension'      => $extension,
			'zipcode'        => $zipcode,
			'city'           => $city,
		);
	}

	/**
	 * Adds an email address to the person
	 *
	 * @param mixed $id
	 * @param mixed $emailtype_id
	 * @param mixed $email
	 */
	function addEmailaddresses( $id = null, $emailtype_id = null, $email = null ) {
		if ( $id === null ) {
			$id = rand( - 1, - 99999 );
		}
		$this->Emailaddresses[] = array(
			'id'           => $id,//to relate to preferred address
			'emailtype_id' => $emailtype_id,
			'email'        => $email
		);
	}

	/**
	 * Adds an phonenumber to the person
	 *
	 * @param mixed $id
	 * @param mixed $phonenumbertype_id
	 * @param mixed $phonenumber
	 */
	function addPhonenumber( $id = null, $phonenumbertype_id = null, $phonenumber = null ) {
		if ( $id === null ) {
			$id = rand( - 1, - 99999 );
		}
		$this->Phonenumbers[] = array(
			'id'                 => $id, //to relate to preferred address
			'phonenumbertype_id' => $phonenumbertype_id,
			'phonenumber'        => $phonenumber
		);
	}

	/**
	 * Set the Job document
	 *
	 * @param mixed $name
	 * @param mixed $type
	 * @param mixed $content
	 */
	function setJob( $name = null, $document = array() ) {
		$this->Job = array(
			'name'     => $name,
			'document' => $document
		);
	}

	/**
	 * Set the Logo document
	 *
	 * @param mixed $name
	 * @param mixed $type
	 * @param mixed $content
	 */
	function setLogo( $name = null, $type = null, $content = null ) {
		$this->Company['Logo'] = array(
			'name'    => $name,
			'type'    => $type,
			'content' => $content //base64 encoded
		);
	}

	/**
	 * adds a competence to the applicant
	 *
	 * @param mixed $competence_id
	 */
	function addCompetence( $competence_id = null ) {
		if ( isset( $competence_id ) && is_numeric( $competence_id ) && $competence_id > 0 ) {
			$this->Competences[] = array(
				'id' => $competence_id
			);
		}
	}

	/**
	 * sets a match or something (connection between job and applicant)
	 *
	 * @param mixed $jobId
	 * @param mixed $motivation
	 */
	function setApplication( $jobId = null, $applicantId = null, $motivation = '', $url = '' ) {
		$this->Application = array(
			'job_id'       => $jobId,
			'applicant_id' => $applicantId,
			'motivation'   => $motivation,
			'url'          => $url
		);
	}

	/**
	 * creates the complete structure that can be posted to EMOL3
	 *
	 */
	function createSubscription() {

		$this->Company['Addresses']      = $this->Addresses;
		$this->Company['Emailaddresses'] = $this->Emailaddresses;
		$this->Company['Phonenumbers']   = $this->Phonenumbers;

		if ( isset( $this->Addresses[0]['id'] ) ) {
			$this->Company['preferedaddres_id'] = $this->Addresses[0]['id'];
		}
		if ( isset( $this->Emailaddresses[0]['id'] ) ) {
			$this->Company['preferedemailaddress_id'] = $this->Emailaddresses[0]['id'];
		}
		if ( isset( $this->Phonenumbers[0]['id'] ) ) {
			$this->Company['preferedphonenumber_id'] = $this->Phonenumbers[0]['id'];
		}


		//set the person
		$this->Contact['Person'] = $this->Person;

		$return = array(
			'Contact'     => $this->Contact,
			'Job'         => $this->Job,
			'Application' => $this->Application,
		);

		$return = array_merge( $this->Company, $return );

		return $return;
	}


	/**
	 * creates the complete structure that can be posted to EMOL3
	 *
	 */
	function createCompany() {

		//compile addresses
		$this->Person['Addresses']      = $this->Addresses;
		$this->Person['Emailaddresses'] = $this->Emailaddresses;
		$this->Person['Phonenumbers']   = $this->Phonenumbers;

		//set the person
		$this->Contact['Person'] = $this->Person;

		//compile the profile
		$this->Company['Onlineprofile'] = $this->Onlineprofiles;

		//return the applicant + person
		return $this->Company;
	}


}

