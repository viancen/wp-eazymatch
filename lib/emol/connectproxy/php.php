<?php

/**
 *
 * Provides a proxy to the EazyCore, the proxy automatically catches SoapFaults
 *
 * @author Rob van der Burgt
 *
 */
class emol_connectproxy_php extends emol_connectproxy_rest {
	protected $format = 'php';

	/**
	 * decodes the api call output
	 *
	 * @var string $input
	 * @return mixed[]
	 */
	protected function decodeResult( $input ) {
		return unserialize( $input );
	}

	/**
	 * encodes an argument to an string format
	 *
	 * @param string $input
	 *
	 * @return string
	 */
	protected function encodeArgument( $input ) {
		return serialize( $input );
	}
}