<?php

class emol_Level2Listboxes {

	//array of lists
	var $lists = '';

	//base url
	var $baseUrl = '';

	//side
	var $side = '';

	/* default constructor */
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
				$return .= '<div class="emol-competence-list-' . $item['id'] . '">';
				//listbox
				$return .= '<label class="emol-search-part-label" for="emol-comptetence-' . $item['id'] . '">' . $item['name'] . '</label>';
				$return .= '<select id="emol-comptetence-' . $item['id'] . '" class="' . $classname . '">';
				$return .= '<option value="">&nbsp;</option>';

				foreach ( $item['children'] as $it ) {
					$sel = '';
					if ( in_array( $it['id'], $currentCompetences ) ) {
						$sel = 'selected="selected"';
					}
					$return .= '<option value="' . $it['id'] . '" ' . $sel . '>' . $it['name'] . '</option>';

				}
				$return .= '</select>';
				$return .= '</div>';
			}
		}
		$this->lists = $return;
	}

}