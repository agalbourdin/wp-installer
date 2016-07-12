WordPress Installer
===================

Install and configure a new WordPress website, with a custom blank theme.

Execute `./install.sh` to run installer.

Make sure `install.sh` is executable (`chmod +x install.sh`).

## What it does

### Download & Install WordPress

* Install the latest stable release of WordPress in the desired locale
* Merge source files with the base application located in `src`

### Configure WordPress

* Database configuration with random table prefix
* Timezone setting
* Posts Revisions limit
* Custom theme name
* Custom wp-content name
* Auto-generated security keys
* Create administrator account and update its ID
* Permalink configuration and menus initialization (header and footer)
* Comments disabled by default
* Reset default image sizes (auto-resize disabled)

## Theme

HTML5 ready blank theme with [HTML 5 Boilerplate](https://github.com/h5bp/html5-boilerplate). This theme include:

* Favicon, Apple Touch Icon, Normalize.css, jQuery, Modernizr
* `<head>` cleaned from comments, WP versions, feeds...
* Admin bar removed from front office
* i18n ready with theme text domain
* XMLRPC disabled

* Helpers
 * Micro-template loader
 * Generic methods: truncate, encode/decode
 * Mails with HTML template

* .htaccess
 * Protection of wp-includes, .htaccess, install.php, wp-config.php
 * Directory listing disabled
 * Optimizations: ETags, Expires headers, P3P, Mime types...
