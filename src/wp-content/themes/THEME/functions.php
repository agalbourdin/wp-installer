<?php
/**
 * Load CSS and JS scripts.
 *
 * Files can be loaded only for specific pages, based on Body Class.
 */
add_action('wp_enqueue_scripts', 'registerFrontDeps');
function registerFrontDeps()
{
    $styles = array(
        'normalize' => array('path' => '/css/normalize.min.css', 'version' => '3.0.0'),
        'main'      => array('path' => '/css/main.css', 'version' => '1.0')
    );
    $scripts = array(
        'jquery'    => array('path' => '/js/jquery-1.11.0.min.js', 'footer' => true, 'version' => '1.11.0'),
        'modernizr' => array('path' => '/js/modernizr-2.7.1.min.js', 'footer' => false, 'version' => '2.7.1'),
    );

    $bodyClass = get_body_class();

    // Home
    if (in_array('home', $bodyClass)) {

    }

    $scripts['main'] = array('path' => '/js/main.js', 'footer' => true, 'version' => '1.0');

    foreach ($styles as $style => $data) {
        if (wp_style_is($style, 'registered')) {
            wp_deregister_style($style);
        }

        $path = (strpos($data['path'], '//') === 0) ? $data['path'] : get_template_directory_uri() . $data['path'];

        wp_register_style(
            $style,
            $path,
            isset($data['deps']) ? $data['deps'] : array(),
            isset($data['version']) ? $data['version'] : false,
            isset($data['media']) ? $data['media'] : 'all'
        );

        wp_enqueue_style($style);
    }

    foreach ($scripts as $script => $data) {
        if (wp_script_is($script, 'registered')) {
            wp_deregister_script($script);
        }

        $path = (strpos($data['path'], '//') === 0) ? $data['path'] : get_template_directory_uri() . $data['path'];

        wp_register_script(
            $script,
            $path,
            isset($data['deps']) ? $data['deps'] : array(),
            isset($data['version']) ? $data['version'] : false,
            isset($data['footer']) ? $data['footer'] : false
        );

        wp_enqueue_script($script);
    }
}

/**
 * Clean HTML head section.
 */
add_action('init', 'removeHeadLinks');
function removeHeadLinks()
{
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    add_filter('the_generator', '__return_null');
}

if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name'          => 'Sidebar Widgets',
        'id'            => 'sidebar-widgets',
        'description'   => 'These are widgets for the sidebar.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>'
    ));
}

/**
 * Remove meta boxes from Wordpress dashboard
 */
add_action('wp_dashboard_setup', 'dashboardRemoveBoxes');
function dashboardRemoveBoxes()
{
    remove_action('welcome_panel', 'wp_welcome_panel');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
}

/**
 * Language.
 */
add_action('after_setup_theme', 'loadTextDomain');
function loadTextDomain()
{
    load_theme_textdomain(wp_get_theme()->__toString(), get_template_directory() . '/locale');
}

/**
 * Menus.
 */
add_theme_support('menus');
register_nav_menus(array(
    'header' => 'Header',
    'footer' => 'Footer',
));

/**
 * Remove comments on wp_head(), leave conditional comments.
 */
function filterHead()
{
    $patterns = array(
        "/<!--(?!\s*\[).*?-->/s"                => "",
        "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/"    => "\n",
        "/\n+\s*</"                             => "\n" . $pOptions['spacer'] . "<",
    );
    ob_start();
    wp_head();
    echo preg_replace(array_keys($patterns), array_values($patterns), ob_get_clean());
}

/**
 * SMTP Setting.
 */
add_action('phpmailer_init', 'phpMailerInitSmtp');
function phpMailerInitSmtp(PHPMailer $phpmailer)
{
    $phpmailer->Host = 'localhost';
    $phpmailer->Port = 25;
    $phpmailer->IsSMTP();
}

/**
 * Helpers
 */
require(TEMPLATEPATH . '/helpers.php');
require(TEMPLATEPATH . '/helpers_admin.php');
