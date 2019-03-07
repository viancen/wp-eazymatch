<?php

class emol_session {
	/**
	 * name of the session scoping
	 *
	 * @var sting
	 */
	public static $scope = 'emol';

	/**
	 * make sure scope exists
	 */
	static public function checkScope() {
		if ( ! isset( $_SESSION[ emol_session::$scope ] ) ) {
			$_SESSION[ emol_session::$scope ] = array();
		}
	}

	/**
	 * make sure session is available
	 */
	static public function checkSession() {
		//start session if not enabled
		//if (!session_id())
		//   session_start();

		// check scope
		emol_session::checkScope();
	}

	/**
	 * checks if variable is available in session
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	static public function exists( $name ) {
		emol_session::checkScope();

		return isset( $_SESSION[ emol_session::$scope ][ $name ] ) === true;
	}

	/**
	 * checks if variable is available in session
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	static public function isValidId( $name ) {
		// check if session var exists
		if ( ! emol_session::exists( $name ) ) {
			return false;
		}

		// check if var is a number and above zero
		$id = emol_session::get( $name );

		return is_numeric( $id ) && $id > 0;
	}

	/**
	 * check if an applicant is loggedin
	 *
	 * @return bool
	 */
	static public function isApplicant() {
		return emol_session::isValidId( 'applicant_id' );
	}


	/**
	 * check if an contact is loggedin
	 *
	 * @return bool
	 */
	static public function isContact() {
		return emol_session::isValidId( 'contact_id' ) && emol_session::isValidId( 'company_id' );
	}


	/**
	 * check if an person is loggedin
	 *
	 * @return bool
	 */
	static public function isLogedIn() {
		return emol_session::isApplicant() || emol_session::isContact();
	}

	/**
	 * gets an variable from the session
	 *
	 * @param string $name
	 *
	 * @return string variable content or empty string if not existing
	 */
	static public function get( $name ) {
		if ( ! emol_session::exists( $name ) ) {
			return '';
		}

		return $_SESSION[ emol_session::$scope ][ $name ];
	}

	/**
	 * removes an variable from the session
	 *
	 * @param string $name
	 */
	static public function remove( $name ) {
		if ( emol_session::exists( $name ) ) {
			unset( $_SESSION[ emol_session::$scope ][ $name ] );
		}
	}

	/**
	 * sets an variable in an session
	 *
	 * @param mixed $name
	 * @param mixed $value
	 */
	static public function set( $name, $value = '' ) {
		if ( is_array( $name ) ) {
			foreach ( $name as $key => $value ) {
				emol_session::set( $key, $value );
			}

			return;
		}

		emol_session::checkScope();

		$_SESSION[ emol_session::$scope ][ $name ] = $value;
	}

	/**
	 * make sure scope exists
	 */
	static public function terminate() {
		//start session if not enabled
		if ( ! session_id() ) {
			session_start();
		}

		// create empty sessions tring
		$_SESSION[ emol_session::$scope ] = array();
		//session_destroy();
	}
}

// make sure session exists
emol_session::checkSession();