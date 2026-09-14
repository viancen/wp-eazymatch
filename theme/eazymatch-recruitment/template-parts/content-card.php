<?php
/**
 * Post card used in archives, search results and the blog index.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class( 'emr-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="emr-post-card__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'emr-card' ); ?>
		</a>
	<?php endif; ?>

	<div class="emr-post-card__body">
		<?php emr_post_meta(); ?>

		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

		<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>

		<p class="emr-post-card__more">
			<a href="<?php the_permalink(); ?>">
				<?php esc_html_e( 'Lees verder', 'eazymatch-recruitment' ); ?> &rarr;
				<span class="screen-reader-text"><?php the_title(); ?></span>
			</a>
		</p>
	</div>
</article>
