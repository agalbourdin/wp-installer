<?php
get_header();

if (have_posts()) {
	while (have_posts()) {
		the_post();
		template('box/post', array('post' => $post));
	}
}
?>

<nav id="navigation">
	<?php next_posts_link('&laquo; Older Entries') ?>
	<?php previous_posts_link('Newer Entries &raquo;') ?>
</nav>

<?php
get_footer();
