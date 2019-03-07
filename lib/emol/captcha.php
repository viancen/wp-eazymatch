<?php

class emol_captcha {
	/**
	 * unique identifier for this captcha check ( is auto generated )
	 */
	private $captchaID;

	/**
	 * unique captcha bound to the captcha ID
	 */
	public $captchaCode;

	/**
	 * prefix for session variable names
	 */
	private $sessionPrefix = 'captcha';

	/**
	 * name of hidden form field for the captcha id
	 */
	public $inputNameID = 'emol_captcha_id';

	/**
	 * name of hidden form field for the captcha code
	 */
	public $inputNameCode = 'emol_captcha_code';

	/**
	 * generates random strings for captcha codes/ids
	 *
	 * @var int $length
	 * @return string
	 */
	private function randomString( $length = 5 ) {
		// numbers/letters wich look simular are removed from this string ( eg. number 0 vs letter O )
		return substr( str_shuffle( str_repeat( 'ABCDEFGHJKMNPQRSTUVWXYZ234578', 5 ) ), 0, $length );
	}

	/**
	 * construct the capcha class
	 *
	 * @var $captchaID ID of captcha session
	 * @return emol_captcha
	 */
	public function __construct( $captchaID = null ) {
		if ( ! empty( $captchaID ) ) {
			$this->setID( $captchaID );
		} else {
			// check if captcha is found in POST
			if ( isset( $_POST[ $this->inputNameID ] ) && ! empty( $_POST[ $this->inputNameID ] ) ) {
				$this->setID( $_POST[ $this->inputNameID ] );
			} else {
				// if no id specified, or in POST object, generate one
				$this->generate();
			}
		}
	}

	/**
	 * Generate an new captcha
	 */
	public function generate() {
		$newID = $this->randomString( 15 );
		$this->setID( $newID );
	}

	/**
	 * set the current captcha ID to monitor
	 */
	public function setID( $captchaID ) {
		$this->captchaID = $captchaID;

		$captchaCode = emol_session::get( $this->sessionPrefix . $captchaID );

		// generate an captcha code if not found in session
		if ( empty( $captchaCode ) ) {
			$captchaCode = $this->randomString();
			emol_session::set( $this->sessionPrefix . $captchaID, $captchaCode );
		}

		$this->captchaCode = $captchaCode;
	}

	/**
	 * gets the current captcha ID
	 */
	public function getID() {
		return $this->captchaID;
	}

	/**
	 * creates the captcha image resource
	 *
	 * @return resource
	 */
	private function getImage() {
		$im = imagecreatetruecolor( 120, 38 );

		$white = imagecolorallocate( $im, 255, 255, 255 );
		$grey  = imagecolorallocate( $im, 100, 100, 100 );
		$black = imagecolorallocate( $im, 0, 0, 0 );

		imagefilledrectangle( $im, 0, 0, 200, 35, $black );

		//path to font - this is just an example you can use any font you like:
		//$dir = str_replace('/lib','/assets',$dir);
		$font = dirname( dirname( __FILE__ ) ) . '/captcha.ttf';


		imagettftext( $im, 20, 4, 22, 30, $grey, $font, $this->captchaCode );
		imagettftext( $im, 20, 4, 15, 32, $white, $font, $this->captchaCode );

		return $im;
	}

	/**
	 * creates an data URI for the captcha
	 *
	 * @return string
	 */
	public function getDataURI() {
		// generate en temporary name for the capcha image
		$uploadinfo = wp_upload_dir();
		$tempName   = $uploadinfo['basedir'] . '/captcha.' . $this->captchaID . '.gif';

		// generate the capcha image
		$im = $this->getImage();

		// save generated image
		imagegif( $im, $tempName );
		imagedestroy( $im );

		// convert image to DATA URI and delete temp file
		$imgbinary = fread( fopen( $tempName, "r" ), filesize( $tempName ) );
		$image     = base64_encode( $imgbinary );

		unlink( $tempName );

		//return URI
		return 'data:image/gif;base64,' . $image;
	}

	/**
	 * creates an image tag with the captche image URI
	 *
	 * @return string
	 */
	public function getImageTag() {
		return '<img src="' . $this->getDataURI() . '" alt=""/>';
	}

	/**
	 * generates form fields for this captcha
	 *
	 * @return string
	 */
	public function getFormFields() {
		$inputs = '<input type="hidden" name="' . $this->inputNameID . '" id="' . $this->inputNameID . '" value="' . $this->getID() . '" />';
		$inputs .= '<input type="text" name="' . $this->inputNameCode . '" id="' . $this->inputNameCode . '" value="" placeholder="' . EMOL_FILL_IN_CAPTCHA . '" class="required" />';

		return $inputs;
	}

	/**
	 * checks if the captcha is valid
	 *
	 * @var bool $regenerateIfFalse
	 * @return bool
	 */
	public function isValid( $regenerateIfFalse = true ) {
		// check if POST variables are present
		if ( isset( $_POST[ $this->inputNameID ] ) && ! empty( $_POST[ $this->inputNameID ] ) && isset( $_POST[ $this->inputNameCode ] ) && ! empty( $_POST[ $this->inputNameCode ] ) ) {
			// check if ids match
			if ( $_POST[ $this->inputNameID ] == $this->captchaID ) {
				// check if captcha values match, font does not support
				// different characters for upper/lowercase so check is case insensitive
				if ( strtolower( $_POST[ $this->inputNameCode ] ) == strtolower( $this->captchaCode ) ) {
					// check if captcha values match
					return true;
				}
			}
		}

		// captcha not valid, regenerate if needed
		if ( $regenerateIfFalse ) {
			$this->generate();
		}

		return false;
	}
}

?>