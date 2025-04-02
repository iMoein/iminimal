<?php
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$comments_number = get_comments_number();
			printf( _nx( 'یک دیدگاه', '%1$s دیدگاه', $comments_number, 'comments title', 'textdomain' ),
				number_format_i18n( $comments_number ) );
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'kind'       => 'comment',
				'callback'   => 'custom_comment_markup',
				'avatar_size'=> 48,
				'style'      => 'ol',
				'short_ping' => true,
			) );
			?>
		</ol>

		<?php the_comments_navigation(); ?>

	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="no-comments"><?php _e( 'نظرات بسته شده‌اند.', 'textdomain' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- #comments -->

<?php
// ✅ تابع سفارشی برای استایل‌دهی به کامنت‌ها
function custom_comment_markup($comment, $args, $depth) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	$admin_class = ( get_comment_author() === 'admin' ) ? 'admin-reply' : 'user-reply';
	?>
	<<?php echo tag_escape( $tag ); ?> <?php comment_class( $admin_class, $comment ); ?> id="comment-<?php comment_ID(); ?>">
		<div class="comment-body <?php echo esc_attr( $admin_class ); ?>">
			<div class="comment-header">
				<div class="comment-avatar">
					<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
				</div>
				<div>
					<span class="comment-author"><?php comment_author_link(); ?></span>
					<span class="comment-date"><?php echo get_comment_date() . ' در ' . get_comment_time(); ?></span>

				</div>
			</div>

			<div class="comment-text">
				<?php comment_text(); ?>
			</div>

			<div class="comment-footer">
			<?php if ( $args['has_children'] ) : ?>
						<button class="toggle-children" aria-expanded="true">➖</button>
					<?php endif; ?>
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<span class="comment-awaiting-moderation">دیدگاه شما پس از تایید نمایش داده خواهد شد.</span>
				<?php endif; ?>
			</div>
		</div>
	<?php
}
?>