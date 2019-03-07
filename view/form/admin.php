<?php
echo '<div id="icon-edit-pages" class="icon32"><br /></div>';
echo '<h2 class="nav-tab-wrapper">';

$formManager = emol_form_manager::getInstance();
foreach ( $listIds as $listId ) {
	$class = ( $listId == $currentListId ) ? ' nav-tab-active' : '';

	$label = $formManager->get( $listId )->getLabel();
	echo '<a class="nav-tab' . $class . '" href="?page=' . $adminPage . '&listId=' . $listId . '">' . $label . '</a>';
}

$class = ( 'create' == $currentListId ) ? ' nav-tab-active' : '';
echo '<a class="nav-tab menu-add-new' . $class . '" href="?page=' . $adminPage . '&listId=create"><abbr title="' . __( 'Add' ) . '">+</abbr></a>';

echo '</h2>';


if ( $formInstance !== false ) {
	echo '<form method="POST" id="forminstance_persist_form">';
	echo '<input type="hidden" value="forminstance_save" name="emol_action" />';

	$availableFields = $formInstance->getAvailableFields();

	echo '<select id="emol_forminstance_itemadd">';
	echo '<option value="empty" selected="selected">+ ' . __( 'Add' ) . '</option>';
	foreach ( $availableFields as $availableFieldId => $availableFieldOptions ) {
		$label = isset( $availableFieldOptions['label'] ) ? $availableFieldOptions['label'] : $availableFieldId;
		echo '<option value="' . $availableFieldId . '">' . $label . '</option>';
	}
	echo '</select>';
	echo ' &nbsp;|&nbsp; ';
	echo '<label for="forminstance_label">' . strtolower( __( 'Title' ) ) . '</label> ';
	echo '<input type="text" id="forminstance_label" name="label" value="' . $formInstance->getLabel() . '" />';
	echo ' &nbsp;|&nbsp; ';
	echo '<input type="submit" name="submit" value="' . __( 'Save' ) . '" />';

	if ( $currentListId !== 'create' ) {
		echo '<input type="submit" name="submit" value="' . __( 'Delete' ) . '" style="float: right;" />';
	}

	echo '<hr />';

	echo '<div id="emol_forminstance_fields">';
	foreach ( $formInstance->getFields() as $field ) {
		echo '<div class="emol_fieldconfig">';
		//echo '<b class="emol_move_handler" style="cursor: pointer;">&uarr;&darr;</b> &nbsp; ';
		echo '<a class="emol_remove_handler" href="#">' . __( 'Del' ) . '</a> &nbsp; ';
		echo $field->getConfigElement();
		echo '</div>';
	}
	echo '</div>';
	echo '</form>';
}
