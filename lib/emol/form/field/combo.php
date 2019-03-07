<?php

class emol_form_field_combo extends emol_form_field {
	public function getElement() {
		$arguments = '';

		foreach ( $this->getElementArguments() as $name => $value ) {
			$arguments .= $name . '="' . $value . '" ';
		}

		// where to select the values from
		$valueField = $this->getConfig( 'valueField', 'id' );
		$nameField  = $this->getConfig( 'nameField', 'name' );
		$value      = $this->getValue();

		$comboBox = '<select ' . trim( $arguments ) . '>';
		$comboBox .= '<option value=""></option>';

		foreach ( $this->getOptions() as $option ) {
			if ( ! array_key_exists( $valueField, $option ) || ! array_key_exists( $nameField, $option ) ) {
				continue;
			}

			$optionValue = $option[ $valueField ];
			$selected    = $optionValue == $value ? ' selected="selected"' : '';

			$comboBox .= '<option value="' . $optionValue . '"' . $selected . '>';
			$comboBox .= $option[ $nameField ];
			$comboBox .= '</option>';
		}

		$comboBox .= '</select>';

		return $comboBox;
	}

	public function getElementArguments() {
		// default arguments for this element
		$arguments = array(
			'id'    => $this->getId(),
			'name'  => $this->getName(),
			'class' => $this->getClass()
		);

		if ( $this->getConfig( 'allowBlank', true ) === false ) {
			$arguments['required'] = 'required';
		}

		// configured argument
		foreach ( $this->getConfig( 'arguments', array() ) as $name => $value ) {
			$arguments[ $name ] = $value;
		}

		return $arguments;
	}

	public function getOptions() {
		return $this->getConfig( 'options', array() );
	}
}
