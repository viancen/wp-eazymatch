<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

Class emol_connectManager {
	private static $instanceObj;
	private $instance;
	private $key;
	private $secret;
	private $connection = null;

	public function __construct() {
		$this->instance = get_option( 'emol_instance' );
		$this->key      = get_option( 'emol_key' );
		$this->secret   = get_option( 'emol_secret' );
	}

	/**
	 * @return emol_connectManager
	 */
	public static function getInstance() {

		if ( ! isset( self::$instanceObj ) ) {
			self::$instanceObj = new emol_connectManager();
		}

		return self::$instanceObj;
	}

	public function getConnection() {
		if ( empty( $this->connection ) ) {
			$this->reconnect();
		}

		return $this->connection;
	}

	public function reconnect() {
		// check if apiKey is present and instanceName is not empty
		if ( strlen( $this->key ) < 6 || strlen( $this->instance ) < 3 || strlen( $this->secret ) < 4 ) {
			if ( is_admin() ) {
				eazymatch_trow_error( 'Eazymatch connection settings incorrect.' );
			}

			return null;
		}

		if ( emol_session::exists( 'api_hash' ) ) {
			$apiKey = emol_session::get( 'api_hash' );
		} elseif ( ( $apiKey = get_option( 'emol_apihash', false ) ) == false ) {
			//get new token
			$apiConnect = new emol_connect( $this->key, $this->instance );
			$tempToken  = $apiConnect->session->getToken( $this->key );
			$apiKey     = hash( 'sha256', $tempToken . $this->secret );
			update_option( 'emol_apihash', $apiKey );
		}
		if ( $this->connection == null ) {
			$this->connection = new emol_connect( $apiKey, $this->instance );
		} else {
			$this->connection->setKey( $apiKey );
		}

		return $apiKey;
	}

	public function resetConnection( $forceReconnect = true ) {
		delete_option( 'emol_apihash' );

		if ( $forceReconnect ) {
			$this->reconnect();
		}
	}

	public function resetUserConnection( $forceReconnect = true ) {
		emol_session::remove( 'api_hash' );

		if ( $forceReconnect ) {
			$this->reconnect();
		}
	}

	/**
	 * @depricated
	 */
	public function setToken( $token, $forceReconnect = true ) {
		emol_session::set( 'api_hash', hash( 'sha256', $token . $this->secret ) );

		if ( $forceReconnect ) {
			$this->reconnect();
		}
	}

	public function setUserToken( $token, $forceReconnect = true ) {
		emol_session::set( 'api_hash', hash( 'sha256', $token . $this->secret ) );

		if ( $forceReconnect ) {
			$this->reconnect();
		}
	}

	// functie om singleton af te sluiten, let op, alle gekopieerde instancies moeten ook afgesloten worden
	public function destroy() {
		self::$instance = null;
	}
}


/**
 *
 * Provides a proxy to the EazyCore by autocreating soapclients
 *
 * @author Rob van der Burgt
 *
 */
Class emol_connect {
	private $apiKey = '';
	public $instanceName = '';
	private $serviceNames = array();

	/**
	 * contructe the connection to the eazycore
	 *
	 * @param string $serviceUrl url of the eazycore
	 */
	public function __construct( $key, $instance ) {
		$this->apiKey       = $key;
		$this->instanceName = $instance;
	}

	/**
	 * Magic function to autocreate class objects for soap services
	 */
	public function &__get( $serviceName ) {
		// generate a new emol_connectProxy to provide access to the Core controller
		$this->{$serviceName} = new emol_connectproxy_json( $this->instanceName, $this->apiKey, $serviceName );

		if ( ! in_array( $serviceName, $this->serviceNames ) ) {
			$this->serviceNames[] = $serviceName;
		}

		// return the object
		return $this->{$serviceName};
	}

	public function get( $serviceName ) {
		if ( isset( $this->{$serviceName} ) ) {
			return $this->{$serviceName};
		} else {
			return $this->__get( $serviceName );
		}
	}

	public function setKey( $key ) {
		$this->apiKey = $key;

		foreach ( $this->serviceNames as $serviceName ) {
			$service = $this->get( $serviceName );
			$service->setKey( $key );
		}
	}
}

Class emol_trunk {
	private $emol_connect;
	private $calls = array();
	private $response = array();

	/**
	 * request an emol_connect object for requesting Trunks
	 *
	 * @param emol_connect $emol_connect
	 *
	 * @return emol_trunk
	 */
	public function __construct() {
		$this->emol_connect = eazymatch_connect();
	}

	public function &request( $class, $method, $arguments = array() ) {
		$this->calls[] = array(
			'class'     => $class,
			'method'    => $method,
			'arguments' => $arguments
		);

		$this->response[] = null;

		return $this->response[ count( $this->response ) - 1 ];
	}

	public function execute() {
		$responses = $this->emol_connect->tool->trunk( $this->calls );

		if ( $responses !== null ) {
			$counter = - 1;
			foreach ( $responses as $response ) {
				$counter ++;
				$this->response[ $counter ] = $response;
			}
		} else {
			$this->response = array( 'error: null response' );
		}
	}
}

class_alias( 'emol_trunk', 'EazyTrunk' );
