<?php

class emol_form_field_eazy_emailaddress extends emol_form_field {
	/**
	 * current value of field
	 * @var mixed
	 */
	protected $value = array(
		'id'           => null,
		'email'        => null,
		'emailtype_id' => null,
		'signature'    => null
	);

	protected $fields = array();

	public function __construct( $config = array() ) {
		parent::__construct( $config );

		$name = $this->getName();

		$this->fields = array(
			'email' => new emol_form_field_email( array(
				'id' => $name . 'email'
			) )
		);
	}

	public function setValue( $emailaddress ) {
		$this->value = $emailaddress;

		$fields = $this->fields;

		$fields['email']->setValue( $emailaddress['email'] );
	}

	public function getValue() {
		$value  = $this->value;
		$fields = $this->fields;

		$value['email'] = $fields['email']->getValue();

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
		$element = $fields['email']->getElement();

		return $element;
	}
}