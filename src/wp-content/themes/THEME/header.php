<!doctype html>
<html class="no-js" lang="">
    <head>
    	<meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
    	<title><?php is_home() ? bloginfo('name') : wp_title('') ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

    	<?php
    	if (is_search()) {
    		?>
    		<meta name="robots" content="noindex, nofollow">
    		<?php
    	}

    	if (is_singular()) {
            wp_enqueue_script('comment-reply');
        }
    	?>

        <?php function_exists('filterHead') ? filterHead() : wp_head() ?>

    	<script>
            var HOME_URL     = '<?php echo home_url() ?>/';
            var TEMPLATE_URL = '<?php echo get_template_directory_uri() ?>/';
        </script>

        <link rel="shortcut icon" href="<?php echo home_url() ?>/favicon.ico" type="image/x-icon">
        <link rel="apple-touch-icon" href="<?php echo home_url() ?>/apple-touch-icon.png">
    </head>

    <body <?php body_class() ?>>

    	<header>
    		<nav>
                <?php
                wp_nav_menu(array(
                    'theme_location'  => 'header',
                    'container_class' => 'inner'
                ));
                ?>
            </nav>
    	</header>

    	<div id="page">
            <section id="content">
