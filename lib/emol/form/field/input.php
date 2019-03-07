<?php

abstract class emol_form_field_input extends emol_form_field {
	protected $inputType = '';


	public function setInputType( $inputType ) {
		$this->inputType = $inputType;
	}

	public function getElement() {
		$arguments = '';

		foreach ( $this->getElementArguments() as $name => $value ) {
			$arguments .= $name . '="' . $value . '" ';
		}

		return '<input class="emol-account-input" ' . trim( $arguments ) . ' />';
	}

	public function getElementArguments() {
		// default arguments for this element
		$arguments = array(
			'id'    => $this->getId(),
			'name'  => $this->getName(),
			'value' => $this->getSafeValue(),
			'class' => $this->getClass(),
			'type'  => $this->inputType
		);

		if ( $this->getConfig( 'allowBlank', true ) === false ) {
			$arguments['required'] = 'required';
		}

		if ( $this->getConfig( 'placeholder', false ) !== false ) {
			$arguments['placeholder'] = $this->getConfig( 'placeholder' );
		}


		// configured argument
		foreach ( $this->getConfig( 'arguments', array() ) as $name => $value ) {
			$arguments[ $name ] = $value;
		}

		return $arguments;
	}
}
