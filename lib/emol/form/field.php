<?php

abstract class emol_form_field {
	/**
	 * current value of field
	 * @var mixed
	 */
	protected $value;

	/**
	 * current field configuration
	 * @var mixed
	 */
	protected $config = array();

	/**
	 * construct the field and set the configuration
	 */
	public function __construct( $config = array() ) {
		$this->setConfig( $config );
	}

	public function getValue() {
		return $this->value;
	}

	public function getSafeValue() {
		return htmlspecialchars( $this->getValue() );
	}

	public function setValue( $value ) {
		$this->value = (string) $value;
	}

	/**
	 * detect the value of this field in the postObject
	 */
	public function detectPostValue() {
		$value = emol_post( $this->getName(), false );

		if ( $value !== false ) {
			$this->setValue( $value );
		}
	}

	/**
	 * returns the config or a single key
	 */
	public function getConfig( $key = null, $default = null ) {
		if ( empty( $key ) ) {
			return $this->config;
		}

		if ( array_key_exists( $key, $this->config ) ) {
			return $this->config[ $key ];
		}

		return $default;
	}

	/**
	 * sets the configuration of this field
	 */
	public function setConfig( $config, $value = null, $replace = false ) {
		if ( ! is_array( $config ) ) {
			$config = array( $config => $value );
		}

		if ( array_key_exists( 'value', $config ) ) {
			$this->setValue( $config['value'] );
			unset( $config['value'] );
		}

		if ( $replace ) {
			$this->config = $config;
		} else {
			$this->config = array_merge( $this->config, $config );
		}
	}

	// some config getters/setters shortcuts

	/**
	 * @return string the id name of this field
	 */
	public function getId() {
		return $this->getConfig( 'id', '' );
	}

	/**
	 * @param int $id id name of this field
	 */
	public function setId( $id ) {
		$this->setConfig( 'id', $id );
	}

	/**
	 * get the name of this field, if not found it will return the current id
	 *
	 * @return string the name of this field
	 */
	public function getName() {
		$name = $this->getConfig( 'name' );

		if ( empty( $name ) ) {
			$name = $this->getId();
		}

		return $name;
	}

	/**
	 * @param int $name name of this field
	 */
	public function setName( $name ) {
		$this->setConfig( 'name', $name );
	}

	protected function getClassList() {
		$class = $this->getConfig( 'class', array() );
		$class = is_array( $class ) ? $class : array( $class );

		return $class;
	}

	public function getClass() {
		$class = $this->getClassList();

		return implode( ' ', $class );
	}

	public function setClass( $class, $replace = false ) {
		if ( $replace ) {
			$class = is_array( $class ) ? $class : array( $class );
		} else {
			$classList    = $this->getClassList();
			$classList [] = $class;

			$class = $classList;
		}

		$this->setConfig( 'class', $class );
	}

	/**
	 * checks if this field is a valid field
	 */
	public function isValid() {
		if ( $this->getConfig( 'allowBlank', true ) == false ) {
			$value = trim( $this->getValue() );

			if ( is_string( $value ) ) {
				$value = trim( $value );
			}

			if ( empty( $value ) ) {
				return false;
			}
		}

		return true;
	}

	public function validate() {
		if ( ! $this->isValid() ) {
			$this->setClass( 'invalid' );

			return false;
		}

		return true;
	}

	/**
	 * gets the configuration element for the admin panel
	 */
	public function getConfigElement() {
		$thr         = rand( 1000, 10000000 );
		$fieldConfig = '';
		//$fieldConfig .= '<input name="fieldconfig[][type]" type="hidden" value="' . get_class($this) . '" />';
		$fieldConfig .= '<input name="fieldconfig[' . $thr . '][id]" type="hidden" value="' . $this->getConfig( 'originalId' ) . '" />';
		$fieldConfig .= '<input name="fieldconfig[' . $thr . '][label]" type="text" value="' . $this->getConfig( 'label' ) . '" />';

		return $fieldConfig;
	}

	/**
	 * get the string representation of the field
	 * @return string
	 */
	abstract public function getElement();
}