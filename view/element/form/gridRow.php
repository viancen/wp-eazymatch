<div
        class="emol_grid_row"><?php foreach ( $row as $columnName => $column ) : ?><?php if ( $column instanceof emol_form_field_hidden == false ) {
		continue;
	} ?><?php echo $column->getElement() ?><?php endforeach ?><?php foreach ( $row as $columnName => $column ) : ?><?php if ( $column instanceof emol_form_field_hidden ) {
		continue;
	} ?>
        <div
        class="emol_grid_col emol_grid_col_<?php echo $columnName ?>"><?php echo $column->getElement() ?></div><?php endforeach ?>
    <div class="emol_grid_row_nav"><a href="" class="emol-button button-grid-remove">-</a></div>
</div>