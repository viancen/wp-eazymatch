<?php

abstract class emol_form_field_combo_eazylist extends emol_form_field_combo {
	/**
	 * core list name to create the combo for
	 * set this name when extending this class
	 *
	 * @var string $listname
	 */
	protected $listName;

	public function getOptions() {
		return emol_data_list::get( $this->listName );
	}
}
