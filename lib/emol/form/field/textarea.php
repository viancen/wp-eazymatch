<?php

class emol_form_field_textarea extends emol_form_field {
	public function getElement() {
		$arguments = '';

		foreach ( $this->getElementArguments() as $name => $value ) {
			$arguments .= $name . '="' . $value . '" ';
		}


		return '<textarea ' . trim( $arguments ) . '>' . $this->getSafeValue() . '</textarea>';
	}

	public function getElementArguments() {
		// default arguments for this element
		$arguments = array(
			'id'    => $this->getId(),
			'name'  => $this->getName(),
			'class' => $this->getClass(),
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