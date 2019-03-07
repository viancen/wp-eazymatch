<?php

class emol_form_field_combo_gender extends emol_form_field_combo {
	public function getOptions() {
		return array(
			array(
				'id'   => 'm',
				'name' => EMOL_ACCOUNT_GENDER_MALE
			),
			array(
				'id'   => 'f',
				'name' => EMOL_ACCOUNT_GENDER_FEMALE
			)
		);
	}
}
