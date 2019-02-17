<?php

if ( post_password_required() ) {
	return;
}

echo ' <div id="comments" class="comments-area">';

if ( have_comments() ) {
	echo '<h2 class="comments-title">';

	$comment_count = intval( get_comments_number() );

	printf(
	/* translators: 1: comment count number, 2: title. */
		_n( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $comment_count, 'pagex' ),
		$comment_count,
		'<span>' . get_the_title() . '</span>'
	);

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