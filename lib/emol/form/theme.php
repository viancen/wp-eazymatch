<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Frontend form appearance.
 *
 * Existing sites keep the jQuery UI skin. Optionally a plugin-owned theme
 * can replace that look: Native follows the WordPress site, Atelier and
 * Harbor are designed looks that ship with the plugin.
 */
class emol_form_theme {

	const OPTION  = 'emol_form_theme';
	const JQUERY  = 'jquery';
	const NATIVE  = 'native';
	const ATELIER = 'atelier';
	const HARBOR  = 'harbor';
	const QUARTZ  = 'quartz';
	const SIGNAL  = 'signal';

	/**
	 * @return string[]
	 */
	public static function ids() {
		return array( self::JQUERY, self::NATIVE, self::ATELIER, self::HARBOR, self::QUARTZ, self::SIGNAL );
	}

	/**
	 * Themes that ship CSS from the plugin instead of jQuery UI.
	 *
	 * @return string[]
	 */
	public static function pluginIds() {
		return array( self::NATIVE, self::ATELIER, self::HARBOR, self::QUARTZ, self::SIGNAL );
	}

	/**
	 * @param mixed $value
	 *
	 * @return string
	 */
	public static function sanitize( $value ) {
		$value = is_string( $value ) ? $value : '';

		return in_array( $value, self::ids(), true ) ? $value : self::JQUERY;
	}

	/**
	 * @return string
	 */
	public static function id() {
		return self::sanitize( get_option( self::OPTION, self::JQUERY ) );
	}

	/**
	 * @param string|null $id
	 *
	 * @return bool
	 */
	public static function isPluginTheme( $id = null ) {
		$id = null === $id ? self::id() : $id;

		return in_array( $id, self::pluginIds(), true );
	}

	/**
	 * Admin picker metadata.
	 *
	 * @return array<string,array{label:string,hint:string}>
	 */
	public static function definitions() {
		return array(
			self::JQUERY  => array(
				'label' => defined( 'EMOL_ADMIN_FORM_THEME_JQUERY' ) ? EMOL_ADMIN_FORM_THEME_JQUERY : 'jQuery UI',
				'hint'  => defined( 'EMOL_ADMIN_FORM_THEME_JQUERY_HINT' ) ? EMOL_ADMIN_FORM_THEME_JQUERY_HINT : '',
			),
			self::NATIVE  => array(
				'label' => defined( 'EMOL_ADMIN_FORM_THEME_NATIVE' ) ? EMOL_ADMIN_FORM_THEME_NATIVE : 'Native',
				'hint'  => defined( 'EMOL_ADMIN_FORM_THEME_NATIVE_HINT' ) ? EMOL_ADMIN_FORM_THEME_NATIVE_HINT : '',
			),
			self::ATELIER => array(
				'label' => defined( 'EMOL_ADMIN_FORM_THEME_ATELIER' ) ? EMOL_ADMIN_FORM_THEME_ATELIER : 'Atelier',
				'hint'  => defined( 'EMOL_ADMIN_FORM_THEME_ATELIER_HINT' ) ? EMOL_ADMIN_FORM_THEME_ATELIER_HINT : '',
			),
			self::HARBOR  => array(
				'label' => defined( 'EMOL_ADMIN_FORM_THEME_HARBOR' ) ? EMOL_ADMIN_FORM_THEME_HARBOR : 'Harbor',
				'hint'  => defined( 'EMOL_ADMIN_FORM_THEME_HARBOR_HINT' ) ? EMOL_ADMIN_FORM_THEME_HARBOR_HINT : '',
			),
			self::QUARTZ  => array(
				'label' => defined( 'EMOL_ADMIN_FORM_THEME_QUARTZ' ) ? EMOL_ADMIN_FORM_THEME_QUARTZ : 'Quartz',
				'hint'  => defined( 'EMOL_ADMIN_FORM_THEME_QUARTZ_HINT' ) ? EMOL_ADMIN_FORM_THEME_QUARTZ_HINT : '',
			),
			self::SIGNAL  => array(
				'label' => defined( 'EMOL_ADMIN_FORM_THEME_SIGNAL' ) ? EMOL_ADMIN_FORM_THEME_SIGNAL : 'Signal',
				'hint'  => defined( 'EMOL_ADMIN_FORM_THEME_SIGNAL_HINT' ) ? EMOL_ADMIN_FORM_THEME_SIGNAL_HINT : '',
			),
		);
	}

	/**
	 * @return string
	 */
	public static function brandColor() {
		$color = get_option( 'emol_theme_brand_color' );

		if ( is_string( $color ) && preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color ) ) {
			return $color;
		}

		return '';
	}

	/**
	 * CSS custom properties derived from plugin settings.
	 *
	 * @return string
	 */
	public static function inlineCss() {
		$color = self::brandColor();

		if ( $color === '' ) {
			return '';
		}

		return 'body.emol-form-theme{--emol-theme-brand:' . $color . ';}';
	}

	/**
	 * @param string[] $classes
	 *
	 * @return string[]
	 */
	public static function bodyClass( $classes ) {
		if ( ! self::isPluginTheme() ) {
			return $classes;
		}

		$classes[] = 'emol-form-theme';
		$classes[] = 'emol-form-theme-' . self::id();

		return $classes;
	}

	public static function boot() {
		if ( ! self::isPluginTheme() ) {
			return;
		}

		add_filter( 'body_class', array( __CLASS__, 'bodyClass' ) );
	}
}
