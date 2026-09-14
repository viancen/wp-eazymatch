<?php
/**
 * Fallback template, also used for the blog index.
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
		<h1>
			<?php
			if ( is_home() && get_option( 'page_for_posts' ) ) {
				echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) );
			} else {
				esc_html_e( 'Nieuws', 'eazymatch-recruitment' );
			}
			?>
		</h1>
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
