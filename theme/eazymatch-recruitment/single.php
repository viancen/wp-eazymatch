<?php
/**
 * Single post.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$emr_has_sidebar = is_active_sidebar( 'sidebar-1' );
?>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<div class="emr-page-hero">
		<div class="emr-container">
			<?php emr_breadcrumbs(); ?>
			<h1><?php the_title(); ?></h1>
			<p class="emr-page-hero__meta">
				<time datetime="<?php echo esc_attr( (string) get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( (string) get_the_date() ); ?></time>
				&middot; <?php echo esc_html( (string) get_the_author() ); ?>
			</p>
		</div>
	</div>

	<div class="emr-container">
		<div class="emr-content-area<?php echo $emr_has_sidebar ? ' emr-content-area--sidebar' : ''; ?>">
			<article <?php post_class( 'emr-entry' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="emr-entry__thumb"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>

				<div class="emr-entry__content">
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<nav class="emr-pagination">',
							'after'  => '</nav>',
						)
					);
					?>
				</div>

				<?php
				$emr_categories = get_the_category_list( ', ' );

				if ( $emr_categories ) :
					?>
					<footer class="emr-entry__footer">
						<?php echo wp_kses_post( $emr_categories ); ?>
					</footer>
				<?php endif; ?>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</article>

			<?php get_sidebar(); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
