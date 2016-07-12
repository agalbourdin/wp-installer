<?php
get_header();

if (have_posts()) {
    ?>
    <h2>Search Results</h2>
    <?php
    while (have_posts()) {
        the_post();
        template('box/post', array('post' => $post));
    }
    ?>
    <div class="navigation">
        <div class="next-posts"><?php next_posts_link('&laquo; Older Entries') ?></div>
        <div class="prev-posts"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
    </div>
    <?php
} else {
    ?>
    <h2>No posts found.</h2>
    <?php
}

get_footer();
