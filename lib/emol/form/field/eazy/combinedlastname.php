<?php

class emol_form_field_eazy_combinedlastname extends emol_form_field {
	/**
	 * current value of field
	 * @var mixed
	 */
	protected $value = array(
		'middlename' => null,
		'lastname'   => null
	);

	protected $fields = array();

	public function __construct( $config = array() ) {
		parent::__construct( $config );

		$name = $this->getName();

		$this->fields = array(
			'middlename' => new emol_form_field_text( array(
				'id' => $name . 'middlename'
			) ),
			'lastname'   => new emol_form_field_text( array(
				'id' => $name . 'lastname'
			) )
		);
	}

	public function setValue( $person ) {
		$this->value['middlename'] = $person['middlename'];
		$this->value['lastname']   = $person['lastname'];

		$fields = $this->fields;

		$fields['middlename']->setValue( $person['middlename'] );
		$fields['lastname']->setValue( $person['lastname'] );
	}

	public function getValue() {
		$value  = $this->value;
		$fields = $this->fields;

		$value['middlename'] = $fields['middlename']->getValue();
		$value['lastname']   = $fields['lastname']->getValue();

		return $value;
	}

	/**
	 * detect the value of this field in the postObject
	 */
	public function detectPostValue() {
		foreach ( $this->fields as $field ) {
			$field->detectPostValue();
		}
	}

	public function getElement() {
		$fields = $this->fields;

		$element = $fields['middlename']->getElement();
		$element .= $fields['lastname']->getElement();

		return $element;
	}
}