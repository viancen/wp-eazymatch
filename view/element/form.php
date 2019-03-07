<?php
$hasFiles = false;

foreach ( $form->getFields() as $field ) {
	if ( $field instanceof emol_form_field_file ) {
		$hasFiles = true;
		break;
	}
}

// quickfix
$hasFiles = true;

?>
<form method="POST"<?php echo $hasFiles ? ' enctype="multipart/form-data"' : '' ?>>
    <div
            class="emol_form_rows"><?php foreach ( $form->getFields() as $field ): ?><?php emol_view_show( 'element/form/row.php', array( 'field' => $field ) ) ?><?php endforeach ?>    </div><?php emol_view_show( 'element/form/submitButton.php', array( 'form' => $form ) ) ?>
</form>