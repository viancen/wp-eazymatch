<?php

/**
 *
 * abstract proxy for the EazyCore
 *
 * @author Rob van der Burgt
 *
 */
abstract class emol_connectproxy_rest
{
	/**
	 *  EazyCore root url
	 *
	 * @var string $serviceUrl
	 */
	private $serviceUrl = '';

	/**
	 * session key to use for requests to the EazyCore
	 *
	 * @var string $apiKey
	 */
	private $apiKey = '';

	/**
	 * servicename this proxy connects to
	 *
	 * @var string $serviceName
	 */
	private $serviceName = '';

	/**
	 * name of instance this proxy connects to
	 *
	 * @var string $instanceName
	 */
	private $instanceName = '';

	/**
	 * is this handler in debug mode?
	 *
	 * @var bool $debug
	 */
	private $debug;

	/**
	 * Maximum number of trys an core call can have ( should be atleast 1 )
	 *
	 * @var int $maxTrys
	 */
	public $maxTrys = 3;

	/**
	 * current retrys issued
	 *
	 * @var int $tryCount
	 */
	private $tryCount = 0;

	/**
	 * contructe the connection to the eazycore
	 *
	 * @param string $serviceUrl url of the eazycore
	 */
	public function __construct($instanceName, $apiKey, $serviceName)
	{
		// create a reference to the error logger
		global $emol_Core;
		global $emol_isDebug;


		$this->serviceUrl = get_option('emol_service_url') ? get_option('emol_service_url') : 'https:://api.eazymatch.cloud';


		$this->debug = $emol_isDebug;

		$this->instanceName = $instanceName;
		$this->apiKey = $apiKey;
		$this->serviceName = $serviceName;
	}

	public function setApiKey($apiKey)
	{
		$this->apiKey = $apiKey;
	}

	public function setKey($apiKey)
	{
		$this->setApiKey($apiKey);
	}

	// magic method to catch all funtion calls
	public function __call($name, $argu)
	{
		// check if apiKey is present and instanceName is not empty
		if (strlen($this->apiKey) < 6 || strlen($this->instanceName) < 3) {
			if (is_admin()) {
				eazymatch_trow_error('Eazymatch connection settings incorrect.');
			}

			// TODO: create better response ( notify error )
			return null;
		}


		return $this->doCall($name, $argu);
	}

	// private method to make a call to the soap object
	private function doCall($name, $argu)
	{

		$this->tryCount++;

		// collect post variables for service
		$fields = array(
			// name of instance
			'instance' => urlencode($this->instanceName),

			// key to use for session
			'key' => $this->apiKey
		);

		// add arugments for method
		if (is_array($argu)) {
			$argumentCounter = -1;

			foreach ($argu as $argument) {
				$argumentCounter++;
				$fields['argument[' . $argumentCounter . ']'] = $this->encodeArgument($argument);
			}
		}

		// transform the field format to POST format
		$fields_string = http_build_query($fields);

		// compile url for apicall
		$this->format = 'json';
		$url = $this->serviceUrl . '/v1/' . $this->serviceName . '/' . $name . '.' . $this->format;

		// get requests and requests to the tool controller should not take llong
		$shortRequest = substr($name, 0, 3) == 'get' || $this->serviceName == 'tool';

		//open connection
		$ch = curl_init();

		// configure curl connection for api call
		curl_setopt_array($ch, array(
			// url for api call
			CURLOPT_URL => $url,

			// add post functionality
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $fields_string,

			//no need for this ssl verifypeer
			CURLOPT_SSL_VERIFYPEER => false,

			// optimize connection
			CURLOPT_CONNECTTIMEOUT => 0,
			CURLOPT_TIMEOUT => 400,

			// force returning the result into an variable
			CURLOPT_RETURNTRANSFER => true
		));

		// execute the api call
		$apiResponse = curl_exec($ch);

		// check if connection error occured
		if (curl_errno($ch)) {

			//delete_option('emol_apihash');
			echo 'Error connecting to eazymatch with code curl code: "' . curl_errno($ch) . '".';


			//close connection
			curl_close($ch);

			die();
		}

		//close connection
		curl_close($ch);

		// decode the result
		$apiOutput = $this->decodeResult($apiResponse);


		//echo $this->serviceName . '->' . $name . '<br />';
		//echo "API call in $time seconds<br />";

		// check if api call failed because session needs to be restored
		if (isset($apiOutput['status']) && $apiOutput['status'] == 'error') {

			
			// if the api request fails, its most likely the session on the core is lost
			// try to reset the connection to the EazyCore
			$connectionManager = emol_connectManager::getInstance();
			$connectionManager->resetUserConnection(false);
			$connectionManager->resetConnection();

			// retry with new session
			$result = $this->doCall($name, $argu);

			return $result;
		}

		// return good results inmediatly
		if (is_array($apiOutput) && array_key_exists('success', $apiOutput) && $apiOutput['success'] === true) {

			if (array_key_exists('result', $apiOutput)) {
				return $apiOutput['result'];
			} else {
				return;
			}
		}


		if ($this->debug) {
			//ob_clean();

			emol_dump(array(
				'error' => 'EazyMatch plugin',
				'core' => $this->serviceUrl,
				'service' => $this->serviceName,
				'method' => $name,
				'api_output_decoded' => $apiOutput,
				'api_output_raw' => $apiResponse
			));

			//refresh emol_apihash
			//delete_option( 'emol_apihash' );

			//emol_session::terminate();

			//throw new Exception();

		}
	}

	/**
	 * EazyCore rest request format ( should be php/json/jsonp )
	 *
	 * @var string $format
	 */
	protected $format;

	/**
	 * decodes the api call output
	 *
	 * @param string $input
	 *
	 * @return mixed[]
	 */
	abstract protected function decodeResult($input);

	/**
	 * encodes an argument to an string format
	 *
	 * @param string $input
	 *
	 * @return string
	 */
	abstract protected function encodeArgument($input);
}