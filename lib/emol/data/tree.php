<?php

class emol_data_tree {
	public static $trees = null;

	// ensures the tree data is present
	static public function ensureData() {
		if ( empty( emol_data_tree::$trees ) ) {
			$emol = emol_connectManager::getInstance()->getConnection();

			emol_data_tree::$trees = $emol->tree->all();
		}
	}

	// get an tree from the EazyCore
	static public function get( $treeName ) {
		emol_data_tree::ensureData();

		if ( array_key_exists( $treeName, emol_data_tree::$trees ) ) {
			return emol_data_tree::$trees[ $treeName ][0]['children'];
		}

		return array();
	}
}