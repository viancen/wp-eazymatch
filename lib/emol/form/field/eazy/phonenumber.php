<?php

class emol_form_field_eazy_phonenumber extends emol_form_field {
	/**
	 * current value of field
	 * @var mixed
	 */
	protected $value = array(
		'id'                 => null,
		'phonenumber'        => null,
		'phonenumbertype_id' => null
	);

	protected $fields = array();

	public function __construct( $config = array() ) {
		parent::__construct( $config );

		$name = $this->getName();

		$this->fields = array(
			'phonenumber' => new emol_form_field_phonenumber( array(
				'id' => $name . 'phonenumber'
			) )
		);
	}

	public function setValue( $phonenumber ) {
		$this->value = $phonenumber;

		$fields = $this->fields;

		$fields['phonenumber']->setValue( $phonenumber['phonenumber'] );
	}

	public function getValue() {
		$value  = $this->value;
		$fields = $this->fields;

		$value['phonenumber'] = $fields['phonenumber']->getValue();

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
		$fields  = $this->fields;
		$element = $fields['phonenumber']->getElement();

		return $element;
	}
}