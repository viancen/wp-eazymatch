<div class="emol_grid emol_grid_<?php echo $gridId ?>">
    <div class="emol_grid_template"
         style="display: none;"><?php emol_view_show( 'element/form/gridRow.php', array( 'row' => $templateRow ) ) ?>    </div>
    <div
            class="emol_grid_header">        <?php foreach ( $templateRow as $columnName => $column ) : ?><?php if ( $column instanceof emol_form_field_hidden || $column->getConfig( 'label', '' ) === false ) {
			continue;
		} ?>
            <div
                    class="emol_grid_col emol_grid_col_<?php echo $columnName ?>">            <?php echo $column->getConfig( 'label', '' ); ?>        </div>        <?php endforeach ?>
        <div class="emol_grid_row_nav">&nbsp;</div>
    </div>
    <div
            class="emol_grid_rows">        <?php foreach ( $rows as $row ) : ?><?php emol_view_show( 'element/form/gridRow.php', array( 'row' => $row ) ) ?><?php endforeach ?><?php emol_view_show( 'element/form/gridRow.php', array( 'row' => $emptyRow ) ) ?>    </div>
    <div class="emol_grid_nav"><a class="emol-button button-grid-add" href=""><?php echo EMOL_GRID_ADD ?></a></div>
</div>