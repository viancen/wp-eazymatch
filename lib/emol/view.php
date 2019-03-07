<?php

class emol_view {
	/**
	 * Reference to initated object ( manager )
	 * Used for singleton
	 */
	private static $_instance;

	private $templatePaths = array();

	public static function getInstance() {
		if ( ! self::$_instance ) {
			self::$_instance = new emol_view();
		}

		return self::$_instance;
	}

	public function __construct() {
		$this->addTemplatePath( EMOL_DIR . '/view/' );
	}

	public function addTemplatePath( $path ) {
		array_unshift( $this->templatePaths, $path );
	}

	public function load( $viewName, $data = array() ) {
		foreach ( $this->templatePaths as $path ) {
			if ( ! file_exists( $path . $viewName ) ) {
				continue;
			}

			if ( is_array( $data ) && ! empty( $data ) ) {
				extract( $data );
			}
			ob_start();
			include $path . $viewName;

			return ob_get_clean();
		}

		return '';
	}

	public function show( $viewName, $data = array() ) {
		echo $this->load( $viewName, $data );
	}
}