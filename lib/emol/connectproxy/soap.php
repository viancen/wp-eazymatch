<?php

/**
 *
 * Provides a proxy to the EazyCore, the proxy automatically catches SoapFaults
 *
 * @author Rob van der Burgt
 *
 */
Class emol_connectproxy_soap {
	/**
	 * keeps track of the global debug mode in the plugin
	 *
	 * @var bool
	 */
	private $debug;

	/**
	 * EazyMatch instance name
	 *
	 * @var string
	 */
	private $instanceName = '';

	/**
	 * apiKey used in the connection
	 *
	 * @var string
	 */
	public $apiKey = '';

	/**
	 * service (EazyCore controller) of this object
	 *
	 * @var string
	 */
	private $serviceName = '';

	/**
	 * EazyCore Url
	 *
	 * @var string
	 */
	private $serviceUrl = '';

	/**
	 * Reference to the SoapClient wich is wraped in this object
	 *
	 * @var SoapClient
	 */
	private $service;

	/**
	 * contructe the connection to the eazycore
	 *
	 * @param string $serviceUrl url of the eazycore
	 */
	public function __construct( $instanceName, $apiKey, $serviceName ) {
		// create a reference to the debug switch
		global $emol_isDebug;
		global $emol_Core;
		$this->debug = $emol_isDebug;

		$this->instanceName = $instanceName;

		$this->serviceName = $serviceName;
		$this->serviceUrl  = $emol_Core;


		$this->apiKey = $apiKey;
	}

	public function setKey( $key ) {
		$this->apiKey = $key;

		/**
		 * set SoapClient options
		 * http://nl2.php.net/manual/en/soapclient.soapclient.php#soapclient.soapclient.parameters
		 */

		ini_set( "soap.wsdl_cache_enabled", ( $this->debug == false ? 1 : 0 ) );
		ini_set( 'soap.wsdl_cache_limit', ( $this->debug == false ? 64 : 0 ) );

		$soapOptions = array(
			'cache_wsdl'  => $this->debug ? WSDL_CACHE_NONE : WSDL_CACHE_BOTH,
			'user_agent'  => $this->apiKey . ',' . $this->instanceName,
			'trace'       => $this->debug ? 1 : 0,
			'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 16,
			'encoding'    => 'utf-8'
		);

		// create object in local variable ( so this method will execute only once but the object stays availible)
		$this->service = new SoapClient( $this->serviceUrl . '/wsdl.php?' . $this->serviceName, $soapOptions );
	}

	public function __call( $name, $argu ) {
		// check if apikey is created
		if ( ! is_object( $this->service ) ) {
			$this->setKey( $this->apiKey );
		}

		// check if apiKey is present and instanceName is not empty
		if ( strlen( $this->apiKey ) < 6 && strlen( $this->instanceName ) < 4 ) {
			if ( is_admin() ) {
				eazymatch_trow_error( 'Eazymatch connection settings incorrect.' );
			}

			return null;
		}

		try {
			$response = call_user_func_array( array( &$this->service, $name ), $argu );
		} catch ( SoapFault $e ) {
			// if the soap request fails, its most likely the session on the core is lost
			// try to reset the connection to the EazyCore
			$connectionManager = emol_connectManager::getInstance();
			$connectionManager->resetConnection();

			try {
				$response = call_user_func_array( array( &$this->service, $name ), $argu );
			} catch ( SoapFault $e ) {

				if ( $this->debug ) {
					ob_Clean();
					var_dump( $e );
					emol_session::terminate();
					exit();
				} else {
					ob_clean();
					header( 'location: /' . get_option( 'emol_company_account_url' ) . '/logout/' );
					exit();
				}
			}

		}

		return $response;
	}
}