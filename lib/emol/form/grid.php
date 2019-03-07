<?php

/**
 *
 */
abstract class emol_form_grid extends emol_form_field {
	protected $idCounter = - 1;

	protected $fieldConfig = array();

	protected $rows = array();

	function setValue( $value ) {
		$value = is_array( $value ) ? $value : array();
		$rows  = array();

		foreach ( $value as $rowId => $rowValue ) {

			if ( $rowId === 'templateid' ) {
				continue;
			}

			if ( ! $this->isEmptyRow( $rowValue ) ) {
				$rows [] = $this->createRow( $rowValue, $rowId );
			}
		}

		$this->rows = $rows;
	}

	function isEmptyRow( $rowColumns ) {
		$empty = true;

		foreach ( $rowColumns as $columnName => $columnValue ) {
			if ( $columnName != 'id' && ! empty( $columnValue ) ) {
				$empty = false;
				break;
			}
		}

		return $empty;
	}

	function getValue() {
		$value = array();

		foreach ( $this->rows as $row ) {
			$rowValue = array();
			foreach ( $row as $columnName => $column ) {
				if ( $column instanceof emol_form_field_file ) {
					$rowValue[ $columnName ] = $column->getPostValue();
				} else {
					$rowValue[ $columnName ] = $column->getValue();
				}
			}
			$value [] = $rowValue;
		}

		return $value;
	}

	// create a row of fields and returns the output
	public function createRow( $values = array(), $idCounter = null ) {
		if ( empty( $idCounter ) && $idCounter !== 0 ) {
			$this->idCounter ++;
			$idCounter = $this->idCounter;
		}
		$id = $this->getId();

		$fields = array();

		$columnConfig = $this->getConfig( 'columnConfig', array() );

		foreach ( $this->fieldConfig as $fieldName => $fieldConfig ) {
			// override the user configured configuration
			if ( isset( $columnConfig[ $fieldName ] ) && is_array( $columnConfig[ $fieldName ] ) ) {
				$fieldConfig = array_merge( $fieldConfig, $columnConfig[ $fieldName ] );
			}

			$fieldConfig['name'] = $id . '[' . $idCounter . '][' . $fieldName . ']';
			$fieldConfig['id']   = $id . '_' . $idCounter . '_' . $fieldName;

			if ( array_key_exists( $fieldName, $values ) && ! empty( $values[ $fieldName ] ) ) {
				$fieldConfig['value'] = $values[ $fieldName ];
			}

			$fields[ $fieldName ] = new $fieldConfig['type']( $fieldConfig );

			if ( $fields[ $fieldName ] instanceof emol_form_field_file ) {
				$fields[ $fieldName ]->setName( $id . '_' . $idCounter . '_' . $fieldName );
				$fields[ $fieldName ]->detectPostValue();
			}
		}

		return $fields;
	}

	public function getElement() {
		return emol_view_load( 'element/form/grid.php', array(
			'templateRow' => $this->createRow( array(), 'templateid' ),
			'emptyRow'    => $this->createRow( array(), - 1 ),
			'rows'        => $this->rows,
			'gridId'      => $this->getId()
		) );
	}

	/**
	 * gets the configuration element for the admin panel
	 */
	public function getConfigElement() {
		$fieldConfig = parent::getConfigElement();

		$fieldConfig .= ' &nbsp;|&nbsp; ' . __( 'Columns' ) . ' &nbsp;';

		foreach ( $this->createRow( array(), 'templateid' ) as $fieldName => $field ) {
			if ( $field instanceof emol_form_field_hidden ) {
				continue;
			}

			$fieldConfig .= '<input name="fieldconfig[rownr][columnConfig][' . $fieldName . '][label]" type="text" value="' . $field->getConfig( 'label', '' ) . '" />';
		}

		return $fieldConfig;
	}

	//$this->createRow(array(), 'templateid')

}
