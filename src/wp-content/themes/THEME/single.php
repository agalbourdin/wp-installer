<?php
get_header();

if (have_posts()) {
	while (have_posts()) {
		the_post();
		template('box/post', array('post' => $post));
		comments_template();
	}
}

get_footer();
