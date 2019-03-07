<?php

class emol_form_field_checktree_competence extends emol_form_field_checktree {
	function fillTree() {
		$this->setTreeItems( emol_data_competence::get() );
	}
}
