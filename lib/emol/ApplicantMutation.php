<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Structure of an applicant to be processed by EMOL3
 * to make life easyer processing data from subscription forms
 *
 * contains:
 *     - Applicant
 *         - Person
 *             - Addresses
 *             - Emailaddresses
 *             - Phonenumbers
 *             - Identifications
 *             - Bankaccounts
 *     - Profile
 *         - Competences
 *         - Schooling
 *         - Experience
 *         - Onlineprofile
 *         - Skill
 *         - Condition
 *     - Documents
 *         - CV
 *         - Picture
 *
 * @author Vincent van den Nieuwenhuizen
 */
class emol_ApplicantMutation {

	public $Applicant;

	//person arrays
	public $Addresses = array();
	public $Emailaddresses = array();
	public $Phonenumbers = array();
	public $Identifications = array();
	public $Bankaccounts = array();


	//profile arrays
	public $Conditions = array();
	public $Skills = array();
	public $Onlineprofiles = array();
	public $Experiences = array();
	public $Schooling = array();
	public $Competences = array();

	//container variables
	public $Person;
	public $Profile;
	public $Documents;

	//the application
	public $Application;

	public $Total;

	/**
	 * Applicant object
	 *
	 * @param mixed $applicantId
	 * @param mixed $intakedate
	 * @param mixed $availablefrom
	 * @param mixed $availableto
	 * @param mixed $title
	 * @param mixed $healthcarereference
	 * @param mixed $linkedinrequesttoken
	 * @param mixed $contactvia
	 */
	function setApplicant(
		$applicantId = null,
		$intakedate = null,
		$availablefrom = null,
		$availableto = null,
		$title = null,
		$healthcarereference = null,
		$linkedinrequesttoken = null,
		$contactvia = null,
		$maritalstatus_id = null,
		$searchlocation = null,
		$salary = null,
		$availablehours = null
	) {
		$this->Applicant = array(
			'id'                   => $applicantId,
			'intakedate'           => $intakedate,
			'availablefrom'        => $availablefrom,
			'availableto'          => $availableto,
			'title'                => $title,
			'healthcarereference'  => $healthcarereference,
			'maritalstatus_id'     => $maritalstatus_id,
			'linkedinrequesttoken' => $linkedinrequesttoken,
			'contactvia'           => $contactvia,
			'maritalstatus_id'     => $maritalstatus_id,
			'searchlocation'       => $searchlocation,
			'salary'               => $salary,
			'availablehours'       => is_numeric( $availablehours ) ? $availablehours : null
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
	 * @param mixed $username
	 * @param mixed $password
	 * @param mixed $gender
	 * @param mixed $managercompany_id
	 */
	function setPerson(
		$personId = null,
		$firstname = null,
		$middlename = null,
		$lastname = null,
		$birthdate = null,
		$username = null,
		$password = null,
		$gender = null,
		$ssn = null,
		$nationality_id = null,
		$managercompany_id = null
	) {

		if ( $birthdate != null ) {
			$birthdate = date( 'Ymd', strtotime( $birthdate ) );
		}

		$this->Person = array(
			'id'                => $personId,
			'managercompany_id' => $managercompany_id,
			'nationality_id'    => $nationality_id,
			'firstname'         => $firstname,
			'lastname'          => $lastname,
			'middlename'        => $middlename,
			'birthdate'         => $birthdate,
			'username'          => $username,
			'ssn'               => $ssn,
			'password'          => str_replace( '**********', '', $password ),
			'gender'            => $gender,
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
		$province_id = null,
		$country_id = null,
		$region_id = null,
		$street = null,
		$housenumber = null,
		$extension = null,
		$zipcode = null,
		$city = null,
		$addresstype_id = null
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
	 * Adds an id
	 *
	 * @param mixed $identificationtype_id
	 * @param mixed $experationdate
	 * @param mixed $number
	 */
	function addIdentification( $identificationtype_id = null, $experationdate = null, $number = null ) {
		$this->Identifications[] = array(
			'identificationtype_id' => $identificationtype_id,
			'experationdate'        => $experationdate,
			'number'                => $number,
		);
	}


	/**
	 * Adds a bankaccount
	 *
	 * @param mixed $bankaccounttype_id
	 * @param mixed $accountnr
	 * @param mixed $city
	 */
	function addBankaccount( $bankaccounttype_id = null, $accountnr = null, $city = null ) {
		$this->Bankaccounts[] = array(
			'bankaccounttype_id' => $bankaccounttype_id,
			'accountnr'          => $accountnr,
			'city'               => $city,
		);
	}

	/**
	 * Set the CV document
	 *
	 * @param mixed $name
	 * @param mixed $type
	 * @param mixed $content
	 */
	function setCV( $name = null, $type = null, $content = null ) {
		$this->Documents['CV'] = array(
			'name'    => $name,
			'type'    => $type,
			'content' => $content //base64 encoded
		);
	}

	/**
	 * Set the Picture document
	 *
	 * @param mixed $name
	 * @param mixed $type
	 * @param mixed $content
	 */
	function setPicture( $name = null, $type = null, $content = null ) {
		$this->Documents['Picture'] = array(
			'name'    => $name,
			'type'    => $type,
			'content' => $content //base64 encoded
		);
	}

	/**
	 * sets the conditions of the applicant
	 *
	 * @param mixed $description
	 * @param mixed $mandatory
	 */
	function addCondition( $description = '', $mandatory = 0 ) {
		$this->Conditions[] = array(
			'mandatory'   => $mandatory,
			'description' => $description
		);
	}


	/**
	 * sets the skills of the applicant
	 *
	 * @param mixed $description
	 * @param mixed $title
	 */
	function addSkill( $description = '', $title = '' ) {
		$this->Skills[] = array(
			'title'       => $title,
			'description' => $description
		);
	}

	/**
	 * adds online / urls for an applicant
	 *
	 * @param mixed $onlineprofiletype_id
	 * @param mixed $url
	 */
	function addOnlineprofile( $onlineprofiletype_id = null, $url = '' ) {
		$this->Onlineprofiles[] = array(
			'onlineprofiletype_id' => $onlineprofiletype_id,
			'url'                  => $url
		);
	}


	/**
	 * Adds working experience or courses of an applicant
	 *
	 * @param mixed $experiencetype_id
	 * @param mixed $visible
	 * @param mixed $function
	 * @param mixed $startdate
	 * @param mixed $enddate
	 * @param mixed $description
	 * @param mixed $company
	 */
	function addExperience( $experiencetype_id = null, $visible = null, $function = null, $startdate = null, $enddate = null, $description = '', $company = '' ) {
		$this->Experiences[] = array(
			'experiencetype_id' => $experiencetype_id,
			'visible'           => $visible,
			'function'          => $function,
			'startdate'         => $startdate,
			'enddate'           => $enddate,
			'description'       => $description,
			'company'           => $company,
		);
	}

	/**
	 * adds a school / education of the applicant
	 *
	 * @param mixed $schoolingtype_id
	 * @param mixed $institute
	 * @param mixed $degree
	 * @param mixed $startdate
	 * @param mixed $enddate
	 */
	function addSchooling( $schoolingtype_id = null, $institute = null, $degree = null, $startdate = null, $enddate = null ) {
		$this->Schooling[] = array(
			'schoolingtype_id' => $schoolingtype_id,
			'degree'           => $degree,
			'startdate'        => $startdate,
			'enddate'          => $enddate,
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
	function setApplication( $jobId = null, $motivation = '', $url = '' ) {
		$this->Application = array(
			'job_id'     => $jobId,
			'motivation' => $motivation,
			'url'        => $url
		);
	}

	/**
	 * creates the complete structure that can be posted to EMOL3
	 *
	 */
	function createSubscription() {

		/**
		 * Addresses
		 */
		$pa = array();
		if ( isset( $this->Addresses[0] ) && is_array( $this->Addresses[0] ) ) {
			$pa = $this->Addresses[0];
		}
		$this->Person['Addresses']       = $this->Addresses;
		$this->Person['Preferedaddress'] = $pa; //take the first address as the preferred one

		/**
		 * Email
		 */

		$pa = array();
		if ( isset( $this->Emailaddresses[0] ) && is_array( $this->Emailaddresses[0] ) ) {
			$pa = $this->Emailaddresses[0];
		}
		$this->Person['Emailaddresses']       = $this->Emailaddresses;
		$this->Person['Preferedemailaddress'] = $pa; //take the first address as the preferred one

		/**
		 * Phonenumbers
		 */
		$pa = array();
		if ( isset( $this->Phonenumbers[0] ) && is_array( $this->Phonenumbers[0] ) ) {
			$pa = $this->Phonenumbers[0];
		}
		$this->Person['Phonenumbers']        = $this->Phonenumbers;
		$this->Person['Preferedphonenumber'] = $pa; //pref phoine


		//rest
		$this->Person['Identifications'] = $this->Identifications;
		$this->Person['Bankaccounts']    = $this->Bankaccounts;

		//set the person
		$this->Applicant['Person'] = $this->Person;

		//compile the profile
		$this->Profile['Condition']     = $this->Conditions;
		$this->Profile['Skill']         = $this->Skills;
		$this->Profile['Onlineprofile'] = $this->Onlineprofiles;
		$this->Profile['Experience']    = $this->Experiences;
		$this->Profile['Schooling']     = $this->Schooling;
		$this->Profile['Competence']    = $this->Competences;

		$return = array(
			'Applicant'   => $this->Applicant,
			'Profile'     => $this->Profile,
			'Documents'   => $this->Documents,
			'Application' => $this->Application,
		);

		return $return;
	}


	/**
	 * creates the complete structure that can be posted to EMOL3
	 *
	 */
	function createApplicant() {

		//compile addresses
		$this->Person['Addresses']       = $this->Addresses;
		$this->Person['Preferedaddress'] = $this->Addresses[0]; //take the first address as the preferred one
		$this->Person['Emailaddresses']  = $this->Emailaddresses;
		$this->Person['Phonenumbers']    = $this->Phonenumbers;
		$this->Person['Identifications'] = $this->Identifications;
		$this->Person['Bankaccounts']    = $this->Bankaccounts;

		//set the person
		$this->Applicant['Person'] = $this->Person;

		//compile the profile
		$this->Applicant['Condition']     = $this->Conditions;
		$this->Applicant['Skill']         = $this->Skills;
		$this->Applicant['Onlineprofile'] = $this->Onlineprofiles;
		$this->Applicant['Experience']    = $this->Experiences;
		$this->Applicant['Schooling']     = $this->Schooling;
		$this->Applicant['Competence']    = $this->Competences;

		//return the applicant + person
		return $this->Applicant;
	}


}

