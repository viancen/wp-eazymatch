<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Self-hosted ALTCHA (proof-of-work) for EazyMatch forms.
 *
 * No Google keys: the HMAC secret is generated and stored locally.
 * Compatible with the official ALTCHA widget (SHA-256 challenge).
 */
class emol_altcha {

	const OPTION_HMAC = 'emol_altcha_hmac';
	const MAX_NUMBER  = 80000;
	const TTL         = 600;

	/**
	 * Register AJAX challenge endpoint and front-end assets.
	 */
	public static function register() {
		add_action( 'wp_ajax_emol_altcha_challenge', array( __CLASS__, 'ajaxChallenge' ) );
		add_action( 'wp_ajax_nopriv_emol_altcha_challenge', array( __CLASS__, 'ajaxChallenge' ) );
		add_filter( 'script_loader_tag', array( __CLASS__, 'moduleTag' ), 10, 2 );
	}

	/**
	 * HMAC key, created once and stored in the options table.
	 *
	 * @return string
	 */
	public static function hmacKey() {
		$key = get_option( self::OPTION_HMAC );

		if ( ! is_string( $key ) || strlen( $key ) < 32 ) {
			$key = bin2hex( random_bytes( 32 ) );
			update_option( self::OPTION_HMAC, $key, false );
		}

		return $key;
	}

	/**
	 * Build a new challenge for the widget.
	 *
	 * @return array<string, mixed>
	 */
	public static function createChallenge() {
		$expires = time() + self::TTL;
		$salt    = bin2hex( random_bytes( 12 ) ) . '?expires=' . $expires;
		$number  = random_int( 0, self::MAX_NUMBER );
		$challenge = hash( 'sha256', $salt . $number );

		return array(
			'algorithm'  => 'SHA-256',
			'challenge'  => $challenge,
			'salt'       => $salt,
			'signature'  => hash_hmac( 'sha256', $challenge, self::hmacKey() ),
			'maxnumber'  => self::MAX_NUMBER,
		);
	}

	/**
	 * Verify a widget payload (raw JSON or base64-encoded JSON).
	 *
	 * @param mixed $raw Posted altcha field.
	 *
	 * @return bool
	 */
	public static function verify( $raw ) {
		if ( ! is_string( $raw ) || $raw === '' ) {
			return false;
		}

		$raw  = wp_unslash( $raw );
		$json = $raw;
		$b64  = base64_decode( $raw, true );
		if ( $b64 !== false && $b64 !== '' && isset( $b64[0] ) && $b64[0] === '{' ) {
			$json = $b64;
		}

		$data = json_decode( $json, true );
		if ( ! is_array( $data ) ) {
			return false;
		}

		$algorithm = isset( $data['algorithm'] ) ? (string) $data['algorithm'] : '';
		$challenge = isset( $data['challenge'] ) ? (string) $data['challenge'] : '';
		$salt      = isset( $data['salt'] ) ? (string) $data['salt'] : '';
		$signature = isset( $data['signature'] ) ? (string) $data['signature'] : '';
		$number    = isset( $data['number'] ) ? $data['number'] : null;

		if ( $algorithm !== 'SHA-256' || $challenge === '' || $salt === '' || $signature === '' || ! is_numeric( $number ) ) {
			return false;
		}

		if ( ! hash_equals( hash_hmac( 'sha256', $challenge, self::hmacKey() ), $signature ) ) {
			return false;
		}

		if ( ! hash_equals( $challenge, hash( 'sha256', $salt . (string) (int) $number ) ) ) {
			return false;
		}

		if ( preg_match( '/[?&]expires=(\d+)/', $salt, $m ) ) {
			if ( (int) $m[1] < time() ) {
				return false;
			}
		}

		$replay = 'emol_altcha_used_' . hash( 'sha256', $signature . $number );
		if ( get_transient( $replay ) ) {
			return false;
		}
		set_transient( $replay, 1, self::TTL );

		return true;
	}

	/**
	 * AJAX: return a fresh challenge as JSON.
	 */
	public static function ajaxChallenge() {
		nocache_headers();
		wp_send_json( self::createChallenge() );
	}

	/**
	 * Markup for the widget, including the hidden field the widget fills.
	 *
	 * @return string
	 */
	public static function widgetHtml() {
		$url = admin_url( 'admin-ajax.php?action=emol_altcha_challenge' );

		$strings = wp_json_encode(
			array(
				'label'     => 'Ik ben geen robot',
				'verifying' => 'Bezig met controleren…',
				'verified'  => 'Geverifieerd',
				'error'     => 'Verificatie mislukt. Probeer het opnieuw.',
			)
		);

		return '<altcha-widget class="emol-altcha" challengeurl="' . esc_url( $url ) . '" hidefooter strings="' . esc_attr( $strings ) . '"></altcha-widget>';
	}

	/**
	 * Load the widget as an ES module.
	 *
	 * @param string $tag    Script tag.
	 * @param string $handle Script handle.
	 *
	 * @return string
	 */
	public static function moduleTag( $tag, $handle ) {
		if ( $handle !== 'emol-altcha' ) {
			return $tag;
		}

		$tag = str_replace( ' type="text/javascript"', '', $tag );
		if ( strpos( $tag, 'type="module"' ) === false ) {
			$tag = str_replace( '<script ', '<script type="module" ', $tag );
		}

		return $tag;
	}
}

/**
 * Verify the posted ALTCHA field.
 *
 * @param mixed $payload Optional raw payload; defaults to $_POST['altcha'].
 *
 * @return bool
 */
function emol_verify_altcha( $payload = null ) {
	if ( $payload === null ) {
		$payload = emol_post_exists( 'altcha' ) ? emol_post( 'altcha' ) : '';
	}

	return emol_altcha::verify( $payload );
}

emol_altcha::register();
