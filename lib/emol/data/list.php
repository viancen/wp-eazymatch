<?php

class emol_data_list {
	public static $lists = null;

	// ensures the list data is present
	static public function ensureData() {
		if ( empty( emol_data_list::$lists ) ) {
			$emol = emol_connectManager::getInstance()->getConnection();

			emol_data_list::$lists = $emol->list->all();
		}
	}

	// get an list from the EazyCore
	static public function get( $listname ) {
		emol_data_list::ensureData();

		if ( array_key_exists( $listname, emol_data_list::$lists ) ) {
			return emol_data_list::$lists[ $listname ];
		}

		return array();
	}
}