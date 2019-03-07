<?php

class emol_form_field_picture extends emol_form_field_file {
	public function getElement() {
		$pic     = $this->getValue();
		$element = parent::getElement();
		$element .= '<img src="data:image/png;base64,' . $pic . '" />';

		return $element;
	}
}