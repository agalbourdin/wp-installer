<div class="item" id="item-<?php echo $post->ID ?>">
    <h2><a href="<?php echo get_permalink() ?>"><?php echo $post->post_title ?></a></h2>
    <div class="entry">
        <?php
        $content = apply_filters('the_content', $post->post_content);
        $content = str_replace( ']]>', ']]&gt;', $content );
        echo $content;
        ?>
    </div>
</div>
