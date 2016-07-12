<?php
/**
 * DB connection and base URLs by environment.
 */
if (! isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = '';
}

switch ($_SERVER['SERVER_NAME']) {
    default:
        define('WP_DEBUG', false);
        define('DB_NAME', '');
        define('DB_USER', '');
        define('DB_PASSWORD', '');
        define('DB_HOST', '');
        define('WP_SITEURL', '');
        define('WP_HOME', '');
        break;
}

define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

/**
 * Generate keys: https://api.wordpress.org/secret-key/1.1/salt/
 */
define('AUTH_KEY', '');
define('SECURE_AUTH_KEY', '');
define('LOGGED_IN_KEY', '');
define('NONCE_KEY', '');
define('AUTH_SALT', '');
define('SECURE_AUTH_SALT', '');
define('LOGGED_IN_SALT', '');
define('NONCE_SALT', '');

/**
 * encode() / decode() key
 */
define('ENCODE_KEY', '');

/**
 * Generate a unique random prefix (ex: "lpQ1K0Ph_")
 */
$table_prefix  = '';

/**
 * Default language code.
 */
define('WPLANG', '');
setlocale(LC_TIME, WPLANG . '.utf8');
setlocale(LC_CTYPE, WPLANG . '.utf8');

/**
 * Default theme.
 */
define('WP_DEFAULT_THEME', '');

define('DISALLOW_FILE_EDIT', true);
define('WP_AUTO_UPDATE_CORE', false);

if (! defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/**
 * Update WP_CONTENT_FOLDERNAME to rename wp_content directory.
 */
define('WP_CONTENT_FOLDERNAME', '');
define('WP_CONTENT_DIR', ABSPATH . WP_CONTENT_FOLDERNAME);
define('WP_CONTENT_URL', WP_SITEURL . '/' . WP_CONTENT_FOLDERNAME);
define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');

/**
 * Mails sender (used by sendHtmlMail()).
 */
define('MAIL_FROM', '');

/**
 * Limit Posts Revisions.
 */
define('WP_POST_REVISIONS', false);

require_once(ABSPATH . 'wp-settings.php');

if (function_exists('add_filter')) {
    add_filter('xmlrpc_enabled', '__return_false');
}
