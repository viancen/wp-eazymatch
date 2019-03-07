<?php

class emol_form_field_checktree_applicantstatus extends emol_form_field_checktree {
	function fillTree() {
		$this->setTreeItems( emol_data_tree::get( 'Applicantstatus' ) );
	}
}
