<?php

class emol_Level2Checkboxes {

	//array of lists
	var $lists = '';

	//base url
	var $baseUrl = '';

	//side
	var $side = '';

	/* checkboxes */
	function __construct( $treeArray, $side = null, $classname ) {

		if ( $side == null ) {
			$this->side = get_option( 'emol_job_search_url' );
		} else {
			$this->side = $side;
		}

//current units
		$currentCompetences = array();
		$currentparts       = explode( '/', $_SERVER['REQUEST_URI'] );
		foreach ( $currentparts as $subPart ) {
			if ( strstr( $subPart, 'competence' ) ) {
				$otherparts = explode( ',', $subPart );
				foreach ( $otherparts as $another ) {
					if ( is_numeric( $another ) ) {
						$currentCompetences[] = $another;
					}
				}
			}
		}

		$this->baseUrl = $side . '/';

		$return = '';
		foreach ( $treeArray as $rootTree ) {
			foreach ( $rootTree['children'] as $item ) {

				$return .= '<div class="emol-checkbox-list-' . $item['id'] . '">';
				$return .= '<label class="emol-search-part-label" for="emol-comptetence-' . $item['id'] . '">' . $item['name'] . '</label>';

				foreach ( $item['children'] as $it ) {
					$sel = '';
					if ( in_array( $it['id'], $currentCompetences ) ) {
						$sel = 'checked="checked"';
					}

					$return .= '<label class="emol-search-checkbox-label"><input type="checkbox" class="' . $classname . '"  value="' . $it['id'] . '" ' . $sel . '> ' . $it['name'] . '</label>';

				}
				$return .= '</div>';
			}
		}
		$this->lists = $return;
	}
}