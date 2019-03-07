<?php

class emol_form_field_email extends emol_form_field_input {
	protected $inputType = 'email';

	/**
	 * checks if this field is a valid field
	 */
	public function isValid() {
		if ( ! parent::isValid() ) {
			return false;
		}

		$value = trim( $this->getValue() );

		if ( empty( $value ) ) {
			return true;
		}

		//check email format
		if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) == $value ) {
			//check if the domain has MX entries
			$aux = explode( '@', $value );

			return checkdnsrr( $aux[1], 'MX' );
		}

		return false;
	}
}