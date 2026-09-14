<?php
/**
 * Archives.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$emr_has_sidebar = is_active_sidebar( 'sidebar-1' );
?>

<div class="emr-page-hero">
	<div class="emr-container">
		<?php emr_breadcrumbs(); ?>
		<h1><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
		<?php
		$emr_description = get_the_archive_description();

		if ( $emr_description ) {
			echo '<div class="emr-page-hero__meta">' . wp_kses_post( $emr_description ) . '</div>';
		}
		?>
	</div>
</div>

<div class="emr-container">
	<div class="emr-content-area<?php echo $emr_has_sidebar ? ' emr-content-area--sidebar' : ''; ?>">
		<div>
			<?php if ( have_posts() ) : ?>
				<div class="emr-posts">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					?>
				</div>

				<?php emr_pagination(); ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>

		<?php get_sidebar(); ?>
	</div>
</div>

<?php
get_footer();
