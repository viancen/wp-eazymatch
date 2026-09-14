<?php
/**
 * Sidebar.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside class="emr-sidebar" role="complementary">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
