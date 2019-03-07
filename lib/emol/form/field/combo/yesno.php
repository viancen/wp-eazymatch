<?php

class emol_form_field_combo_yesno extends emol_form_field_combo {
	public function getOptions() {
		return array(
			array(
				'id'   => 1,
				'name' => EMOL_YES
			),
			array(
				'id'   => 0,
				'name' => EMOL_NO
			)
		);
	}
}
