<?php

abstract class emol_form_instance {
	// available fields
	protected $availableFields = array();

	// last set fieldconfiguration
	protected $fieldConfig = array();

	// array collection of (active) fields in this form
	protected $fields = array();


	// label/title of this forminstance
	protected $label = '';


	// apply the default config
	public function __construct( $fieldConfig, $label = '' ) {
		$this->setFieldConfig( $fieldConfig );
		$this->setLabel( $label );
	}

	function setFieldConfig( $fieldsConfig ) {
		$this->fieldConfig = $fieldsConfig;
		$this->fields      = array();


		// create all configured fields
		foreach ( $fieldsConfig as $fieldConfig ) {
			$id = isset( $fieldConfig['id'] ) && ! empty( $fieldConfig['id'] ) ? $fieldConfig['id'] : false;

			if ( $id === false ) {
				continue;
			}

			$fieldConfig = array_merge( $this->availableFields[ $id ], $fieldConfig );

			$fieldClass = $fieldConfig['type'];

			$fieldConfig['originalId'] = $fieldConfig['id'];

			// prevent double ids
			$idCounter = 0;
			while ( isset( $this->fields[ $id ] ) ) {
				$idCounter ++;
				$id                = $fieldConfig['originalId'] . $idCounter;
				$fieldConfig['id'] = $id;
			}

			$field = new $fieldClass( $fieldConfig );


			$this->fields [ $id ] = $field;
		}
	}

	public function setLabel( $label ) {
		$this->label = $label;
	}

	public function getLabel() {
		return $this->label;
	}


	public function getDummyField( $id, $fieldConfig = array() ) {
		$fieldConfig = array_merge( $this->availableFields[ $id ], $fieldConfig );

		$fieldClass = $fieldConfig['type'];

		$fieldConfig['originalId'] = $id;

		$field = new $fieldClass( $fieldConfig );

		return $field;
	}

	public function getMappedValues() {
		$mappedValues = array();

		foreach ( $this->getFields() as $field ) {
			$mapping = $field->getConfig( 'mapping', false );
			if ( ! $mapping ) {
				continue;
			}

			$value = $field->getValue();

			$mapping   = explode( '.', $mapping );
			$mapTarget = &$mappedValues;


			foreach ( $mapping as $mapKey ) {
				if ( ! array_key_exists( $mapKey, $mapTarget ) ) {
					$mapTarget[ $mapKey ] = array();
				}

				$mapTarget = &$mapTarget[ $mapKey ];
			}

			$mapTarget = $value;
		}

		return $mappedValues;
	}

	/**
	 * maps data from an array to form fields
	 *
	 * @param array $values
	 */
	protected function mapData( $values ) {
		foreach ( $this->fields as $field ) {
			$mapping = $field->getConfig( 'mapping', false );

			if ( $mapping === false ) {
				continue;
			}

			$mapping   = explode( '.', $mapping );
			$mapTarget = &$values;

			$keyCount = count( $mapping );
			$keyIndex = 0;

			foreach ( $mapping as $mapKey ) {
				$keyIndex ++;

				if ( ! array_key_exists( $mapKey, $mapTarget ) ) {
					break;
				} // no great success

				if ( $keyIndex == $keyCount ) {
					$field->setValue( $mapTarget[ $mapKey ] );
				} else {
					$mapTarget = &$mapTarget[ $mapKey ];
				}
			}
		}
	}


	public function getAvailableFields() {
		return $this->availableFields;
	}

	public function getFieldConfig() {
		return $this->fieldConfig;
	}

	public function getFields() {
		return $this->fields;
	}

	public function getFieldsByType( $fieldType ) {
		$filteredFields = array();

		foreach ( $this->fields as &$field ) {
			if ( $field instanceof $fieldType ) {
				$filteredFields [] = $field;
			}
		}

		return $filteredFields;
	}

	public function getField( $fieldId ) {
		if ( isset( $this->fields[ $fieldId ] ) ) {
			return $this->fields[ $fieldId ];
		}

		return false;
	}

	/**
	 * initalize data in the form
	 */
	public function initData() {
	}

	public function validate() {
		$valid = true;
		foreach ( $this->fields as $field ) {
			if ( ! $field->validate() ) {
				$valid = false;
			}
		}

		return $valid;
	}

	/**
	 * checks if post value has te be initiated
	 */
	public function checkPOST() {
		if ( isset( $_POST ) && count( $_POST ) > 0 ) {
			// if post was set, handle the post request
			$this->handlePOST();

			// validate and return outcome
			return true;
		}

		return false;
	}

	/**
	 * fill the fields with there post value
	 */
	public function handlePOST() {
		foreach ( $this->fields as $field ) {
			$field->detectPostValue();
		}
	}

	abstract public function persist();
}
