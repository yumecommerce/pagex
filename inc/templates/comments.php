<?php

if ( post_password_required() ) {
	return;
}

echo ' <div id="comments" class="comments-area">';
if ( have_comments() ) {
	echo '<h2 class="comments-title">';

	$comment_count = get_comments_number();
	if ( 1 === $comment_count ) {
		printf(
		/* translators: 1: title. */
			esc_html_e( 'One thought on &ldquo;%1$s&rdquo;', 'pagex' ),
			'<span>' . get_the_title() . '</span>'
		);
	} else {
		printf(
		/* translators: 1: comment count number, 2: title. */
			esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $comment_count, 'comments title', 'pagex' ) ),
			intval( $comment_count ),
			'<span>' . get_the_title() . '</span>'
		);
	}

	echo '</h2>';

	the_comments_navigation();

	echo '<ol class="comment-list">';
	wp_list_comments( array(
		'style'       => 'ol',
		'short_ping'  => true,
		'avatar_size' => 50,
	) );
	echo '</ol>';

	the_comments_navigation();

	if ( ! comments_open() ) {
		echo '<p class="no-comments">' . __( 'Comments are closed.', 'pagex' ) . '</p>';
	}
}

comment_form();
echo '</div>';