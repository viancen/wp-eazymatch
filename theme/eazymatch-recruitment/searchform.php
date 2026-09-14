<?php
/**
 * WordPress search form.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$emr_id = 'emr-search-' . wp_rand( 1000, 9999 );
?>
<form role="search" method="get" class="emr-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $emr_id ); ?>">
		<?php esc_html_e( 'Zoeken op de website', 'eazymatch-recruitment' ); ?>
	</label>
	<div style="display:flex;gap:.6rem;flex-wrap:wrap;">
		<input type="search"
			id="<?php echo esc_attr( $emr_id ); ?>"
			class="emr-searchform__input"
			style="flex:1 1 200px;"
			placeholder="<?php esc_attr_e( 'Zoeken&hellip;', 'eazymatch-recruitment' ); ?>"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			name="s" />
		<button type="submit" class="emr-button"><?php esc_html_e( 'Zoeken', 'eazymatch-recruitment' ); ?></button>
	</div>
</form>
