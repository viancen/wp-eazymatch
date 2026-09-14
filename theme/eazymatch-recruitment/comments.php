<?php
/**
 * Comments.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>
<section class="emr-comments" style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--emr-line);">
	<?php if ( have_comments() ) : ?>
		<h2>
			<?php
			printf(
				/* translators: %d: number of comments. */
				esc_html( _n( '%d reactie', '%d reacties', get_comments_number(), 'eazymatch-recruitment' ) ),
				(int) get_comments_number()
			);
			?>
		</h2>

		<ol class="comment-list" style="list-style:none;padding:0;">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
			)
		);
		?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_submit' => 'emr-button',
		)
	);
	?>
</section>
