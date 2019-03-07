<?php

class emol_data_competence {
	public static $tree = null;

	// ensures the tree data is present
	static public function ensureData() {
		if ( empty( emol_data_competence::$tree ) ) {
			$emol = emol_connectManager::getInstance()->getConnection();

			emol_data_competence::$tree = $emol->competence->tree();
		}
	}

	// get an tree from the EazyCore
	static public function get() {
		emol_data_competence::ensureData();

		return emol_data_competence::$tree;
	}
}