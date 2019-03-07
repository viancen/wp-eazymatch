<?php

class emol_form_field_eazy_address extends emol_form_field {
	/**
	 * current value of field
	 * @var mixed
	 */
	protected $value = array(
		'id'             => null,
		'addresstype_id' => null,
		'province_id'    => null,
		'country_id'     => null,
		'region_id'      => null,
		'street'         => null,
		'housenumber'    => null,
		'extension'      => null,
		'zipcode'        => null,
		'city'           => null,
		'latitude'       => null,
		'longtitude'     => null
	);

	protected $fields = array();

	public function __construct( $config = array() ) {
		parent::__construct( $config );

		$name = $this->getName();

		$this->fields = array(
			'street'      => new emol_form_field_text( array(
				'id'          => $name . 'street',
				'placeholder' => EMOL_STREET
			) ),
			'housenumber' => new emol_form_field_text( array(
				'id'          => $name . 'housenumber',
				'placeholder' => EMOL_HOUSENUMBER
			) ),
			'extension'   => new emol_form_field_text( array(
				'id'          => $name . 'extension',
				'placeholder' => EMOL_EXTENSION
			) ),
			'zipcode'     => new emol_form_field_text( array(
				'id'          => $name . 'zipcode',
				'placeholder' => EMOL_ZIPCODE
			) ),
			'city'        => new emol_form_field_text( array(
				'id'          => $name . 'city',
				'placeholder' => EMOL_CITY
			) )
		);
	}

	public function setValue( $address ) {
		$this->value = $address;

		$fields = $this->fields;

		$fields['street']->setValue( $address['street'] );
		$fields['housenumber']->setValue( $address['housenumber'] );
		$fields['extension']->setValue( $address['extension'] );
		$fields['zipcode']->setValue( $address['zipcode'] );
		$fields['city']->setValue( $address['city'] );
	}

	public function getValue() {
		$value  = $this->value;
		$fields = $this->fields;

		$value['street']      = $fields['street']->getValue();
		$value['housenumber'] = $fields['housenumber']->getValue();
		$value['extension']   = $fields['extension']->getValue();
		$value['zipcode']     = $fields['zipcode']->getValue();
		$value['city']        = $fields['city']->getValue();

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

		$element = $fields['street']->getElement();
		$element .= $fields['housenumber']->getElement();
		$element .= $fields['extension']->getElement();
		$element .= '<br />';
		$element .= $fields['zipcode']->getElement();
		$element .= $fields['city']->getElement();

		return $element;
	}
}