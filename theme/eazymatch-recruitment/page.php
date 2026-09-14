<?php
/**
 * Single page.
 *
 * Pages that contain an [eazymatch] shortcode are rendered full width, so the
 * job list, search form and application form get the whole content area.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$emr_is_eazymatch = emr_page_has_eazymatch_view( get_the_ID() );
?>

<div class="emr-page-hero">
	<div class="emr-container">
		<?php emr_breadcrumbs(); ?>
		<h1><?php echo esc_html( emr_page_heading() ); ?></h1>
	</div>
</div>

<div class="emr-container">
	<div class="emr-content-area">
		<article <?php post_class( 'emr-entry' ); ?>>
			<?php if ( ! $emr_is_eazymatch && has_post_thumbnail() ) : ?>
				<div class="emr-entry__thumb"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>

			<div class="emr-entry__content">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();

					wp_link_pages(
						array(
							'before' => '<nav class="emr-pagination">',
							'after'  => '</nav>',
						)
					);
				endwhile;
				?>
			</div>
		</article>
	</div>
</div>

<?php
get_footer();
