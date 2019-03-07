<?php

class emol_form_manager {

	/**
	 * Reference to initated object ( manager )
	 * Used for singleton
	 */
	private static $_instance;

	/**
	 * reference to forminstances ( instance of emol_form_instance )
	 */
	private $instances = array();

	/**
	 * on wich wordpress option should the form manager work?
	 */
	private $optionName = 'emol_forminstances';


	/**
	 * savedInstances for local caching ( persisted in wordpress option )
	 */
	private $savedInstances;

	/**
	 * on wich wordpress option should the form manager work?
	 */
	private $defaultOption = array(
		'lastId'    => 0,
		'instances' => array()
	);


	/**
	 * wich formTypes should be managed
	 */
	private $formTypes = array(
		'emol_forminstance_applicant_account'
	);

	private function __construct() {
		$this->savedInstances = get_option( $this->optionName, false );

		if ( $this->savedInstances === false ) {
			$this->savedInstances = $this->defaultOption;
			$this->persistSavedInstances();
		}
	}

	public static function getInstance() {
		if ( ! self::$_instance ) {
			self::$_instance = new emol_form_manager();
		}

		return self::$_instance;
	}

	public function get( $instanceId ) {
		// test if form instance exists
		if ( array_key_exists( 'form' . $instanceId, $this->savedInstances['instances'] ) ) {
			// test if instance is already created
			if ( ! array_key_exists( 'form' . $instanceId, $this->instances ) ) {
				$formTypeClass = $this->savedInstances['instances'][ 'form' . $instanceId ]['formType'];

				if ( isset( $this->savedInstances['instances'][ 'form' . $instanceId ]['label'] ) ) {
					$instanceLabel = $this->savedInstances['instances'][ 'form' . $instanceId ]['label'];
				} else {
					$instanceLabel = '';
				}
				$config                                  = get_option( $this->optionName . $instanceId, array() );
				$this->instances[ 'form' . $instanceId ] = new $formTypeClass( $config, $instanceLabel );
			}

			// return reference to form instance
			return $this->instances[ 'form' . $instanceId ];
		}

		return false;
	}

	public function getListIds( $type = false ) {
		$idList = array();
		foreach ( $this->savedInstances['instances'] as $formKey => $instanceConfig ) {
			if ( $type === false || $instanceConfig['formType'] == $type ) {
				$idList [] = $instanceConfig['id'];
			}
		}

		return $idList;
	}

	// create an new instance in the database
	public function create( $formType ) {
		if ( in_array( $formType, $this->formTypes ) ) {
			$this->savedInstances['lastId'] ++;
			$this->savedInstances['instances'][ 'form' . $this->savedInstances['lastId'] ] = array(
				'id'       => $this->savedInstances['lastId'],
				'formType' => $formType,
				'label'    => $this->savedInstances['lastId']
			);

			$this->persistSavedInstances();

			return $this->get( $this->savedInstances['lastId'] );
		}

		return false;
	}

	public function remove( $formInstance ) {
		$instanceId = $this->findInstanceId( $formInstance );

		if ( ! is_numeric( $instanceId ) ) {
			return;
		}

		if ( isset( $this->savedInstances['instances'][ 'form' . $instanceId ] ) ) {
			unset( $this->savedInstances['instances'][ 'form' . $instanceId ] );
			$this->persistSavedInstances();
		}

		delete_option( $this->optionName . $instanceId );
	}

	public function findInstanceId( $formInstance ) {
		foreach ( $this->instances as $key => $instance ) {
			if ( $instance == $formInstance ) {
				return str_replace( 'form', '', $key );
			}
		}

		return null;
	}

	/**
	 * save the current instance config
	 */
	public function persistInstanceConfig( $formInstance ) {
		foreach ( $this->instances as $key => $instance ) {
			if ( $instance == $formInstance ) {
				// save the label of the instance
				$this->savedInstances['instances'][ $key ]['label'] = $formInstance->getLabel();
				$this->persistSavedInstances();

				// save the field configuration of the instance
				$fieldConfig = $formInstance->getFieldConfig();

				$instanceId = str_replace( 'form', '', $key );
				update_option( $this->optionName . $instanceId, $fieldConfig );
			}
		}
	}

	/**
	 * save the current instances
	 */
	private function persistSavedInstances() {
		update_option( $this->optionName, $this->savedInstances );
	}
}