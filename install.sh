#!/bin/bash

# Text colors and weight
txtrst=$(tput sgr0)
txtvalid=$(tput setaf 2)
txterror=$(tput setaf 1)
txtbold=$(tput bold)

# Display a success message
success() {
    echo $txtvalid$txtbold$1$txtrst
    echo ""
}

error() {
    echo ""
    echo $txterror$txtbold$1$txtrst
    echo ""
    exit 0
}

# Display a new section message
section() {
    echo $txtbold
    echo "#####################################################################"
    echo "# "$1
    echo "#####################################################################"
    echo $txtrst
}

# Generate random string with custom length/strength
genpasswd() {
    local l=$1
    local s=$2
    [ "$l" == "" ] && l=16
    [ "$s" == "" ] && s=1

    if [ "$s" = 1 ]; then
        tr -dc 'a-zA-Z0-9-!@#$%^*()_+~' < /dev/urandom | head -c ${l} | xargs
    else
        tr -dc 'a-zA-Z0-9' < /dev/urandom | head -c ${l} | xargs
    fi
}

section "Configuration"

# Install in the current directory
DESTINATION="./"
PHAR=$DESTINATION"wp-cli.phar"

###
# Get configuration options from user
###

read -p 'Locale [fr_FR] > ' LOCALE
if [ "$LOCALE" = "" ]; then
    LOCALE="fr_FR"
fi

read -p 'Timezone [Europe/Paris] > ' TIMEZONE
if [ "$TIMEZONE" = "" ]; then
    TIMEZONE="Europe/Paris"
fi

echo ""

read -p 'DB Host [localhost] > ' DBHOST
if [ "$DBHOST" = "" ]; then
    DBHOST="localhost"
fi

read -p 'DB Name > ' DBNAME
if [ "$DBNAME" = "" ]; then
    error "A DB Name is required"
fi

read -p 'DB User > ' DBUSER
if [ "$DBUSER" = "" ]; then
    error "A DB User is required"
fi

read -p 'DB Passord > ' DBPWD

echo ""

read -p 'Site URL > ' URL
if [ "$URL" = "" ] || [[ ! $URL =~ http(s)?://(.*) ]]; then
    error "Site URL must be an URL starting by http or https"
fi
URL=${URL%/}

echo ""

read -p 'Theme name > ' THEME
if [ "$THEME" = "" ]; then
    error "A theme name is required"
fi

echo ""

read -p 'wp-content new name [wp-content] > ' WPCONTENT
if [ "$WPCONTENT" = "" ]; then
    WPCONTENT="wp-content"
elif [ "$WPCONTENT" = "wp-admin" ] || [ "$WPCONTENT" = "wp-includes" ]; then
    error "wp-admin and wp-includes are reserved names"
fi

echo ""

read -p 'Limit Posts Revisions [false] > ' REVISIONS
if [ "$REVISIONS" = "" ] || [ "$REVISIONS" = "false" ] || [ "$REVISIONS" = "0" ]; then
    REVISIONS="false"
elif ! [[ $REVISIONS =~ ^[0-9]+$ ]]; then
    error "You must enter a number"
fi

echo ""

read -p 'Install Plugins: ACF, iTheme Security, Yoast SEO [y/N] > ' PLUGINS
if [ "$PLUGINS" = "" ] || [ "$PLUGINS" = "n" ] || [ "$PLUGINS" = "N" ]; then
    PLUGINS="false"
else
    PLUGINS="true"
fi

echo ""

read -p 'Admin email > ' ADMINMAIL
read -p 'Admin username > ' ADMINUSER

echo ""
read -p 'Ready to install, continue? [Y/n] > ' INSTALL
if [ "$INSTALL" != "" ] && [ "$INSTALL" != "Y" ] && [ "$INSTALL" != "y" ]; then
    exit 0
fi

section "Installation"

###
# Download latest stable WordPress release
###

echo "Downloading..."
echo ""

curl -L https://raw.github.com/wp-cli/builds/gh-pages/phar/wp-cli.phar > $PHAR
php $PHAR core download --locale=$LOCALE

echo ""

###
# Merge WordPress files with custom application
###

echo "Merging Application..."

rsync -aEW $DESTINATION"src/" $DESTINATION

success "OK"

###
# Set WPLANG in wp-config.php
###

echo "Setting Locale..."

WPCONFIG=$DESTINATION"wp-config.php"
sed -i "s/'WPLANG', ''/'WPLANG', '$LOCALE'/g" $WPCONFIG

success "OK"

###
# Set database configuration and site URL in wp-config.php
###

echo "Configuring DB..."

sed -i "s/'DB_NAME', ''/'DB_NAME', '$DBNAME'/g" $WPCONFIG
sed -i "s/'DB_USER', ''/'DB_USER', '$DBUSER'/g" $WPCONFIG
sed -i "s/'DB_PASSWORD', ''/'DB_PASSWORD', '$DBPWD'/g" $WPCONFIG
sed -i "s/'DB_HOST', ''/'DB_HOST', '$DBHOST'/g" $WPCONFIG
sed -i "s#'WP_SITEURL', ''#'WP_SITEURL', '$URL'#g" $WPCONFIG
sed -i "s#'WP_HOME', ''#'WP_HOME', '$URL'#g" $WPCONFIG

success "OK"

###
# Set Posts Revisions limit
###

echo "Configuring Posts Revisions..."

sed -i "s/'WP_POST_REVISIONS', false/'WP_POST_REVISIONS', $REVISIONS/g" $WPCONFIG

success "OK"

###
# Generate random table prefix and add it to wp-config.php
###

echo "Generating DB Prefix..."

PREFIX=`genpasswd 5 0`
sed -i "s/$table_prefix  = ''/$table_prefix  = '${PREFIX}_'/g" $WPCONFIG

success "OK"

###
# Rename theme
###

echo "Setting Directories..."

GITIGNORE=$DESTINATION".gitignore"
sed -i "s/'WP_DEFAULT_THEME', ''/'WP_DEFAULT_THEME', '$THEME'/g" $WPCONFIG
sed -i "s#/THEME/#/$THEME/#g" $GITIGNORE
mv $DESTINATION"wp-content/themes/THEME/" $DESTINATION"wp-content/themes/$THEME/"

success "OK"

###
# Generate and set security keys in wp-config.php
###

echo "Generating Security Keys..."

KEY=`genpasswd 65`
sed -i "s/'AUTH_KEY', ''/'AUTH_KEY', '$KEY'/g" $WPCONFIG

KEY=`genpasswd 65`
sed -i "s/'SECURE_AUTH_KEY', ''/'SECURE_AUTH_KEY', '$KEY'/g" $WPCONFIG

KEY=`genpasswd 65`
sed -i "s/'LOGGED_IN_KEY', ''/'LOGGED_IN_KEY', '$KEY'/g" $WPCONFIG

KEY=`genpasswd 65`
sed -i "s/'NONCE_KEY', ''/'NONCE_KEY', '$KEY'/g" $WPCONFIG

KEY=`genpasswd 65`
sed -i "s/'AUTH_SALT', ''/'AUTH_SALT', '$KEY'/g" $WPCONFIG

KEY=`genpasswd 65`
sed -i "s/'SECURE_AUTH_SALT', ''/'SECURE_AUTH_SALT', '$KEY'/g" $WPCONFIG

KEY=`genpasswd 65`
sed -i "s/'LOGGED_IN_SALT', ''/'LOGGED_IN_SALT', '$KEY'/g" $WPCONFIG

KEY=`genpasswd 65`
sed -i "s/'NONCE_SALT', ''/'NONCE_SALT', '$KEY'/g" $WPCONFIG

KEY=`genpasswd 32`
KEY="$(echo -n "$KEY" | md5sum | awk '{ print $1 }' )"
sed -i "s/'ENCODE_KEY', ''/'ENCODE_KEY', '$KEY'/g" $WPCONFIG

success "OK"

###
# Rename wp-content
###

echo "Renaming wp-content..."

sed -i "s/'WP_CONTENT_FOLDERNAME', ''/'WP_CONTENT_FOLDERNAME', '$WPCONTENT'/g" $WPCONFIG
sed -i "s#wp-content/#$WPCONTENT/#g" $GITIGNORE
if [ "$WPCONTENT" != "wp-content" ]; then
    mv $DESTINATION"wp-content/" $DESTINATION$WPCONTENT"/"
fi

success "OK"

###
# Install WordPress database with auto-generated administrator password
###

echo "Installing DB..."

sed -i "s/'MAIL_FROM', ''/'MAIL_FROM', '$ADMINMAIL'/g" $WPCONFIG

PASSWORD=`genpasswd 8`
php wp-cli.phar core install --url=$URL --admin_user=$ADMINUSER --admin_email=$ADMINMAIL --admin_password=$PASSWORD --title="WordPress"

echo ""

###
# Set permalink structure to %postname%
###

echo "Updating Permalink Structure..."

php wp-cli.phar option update "permalink_structure" "%postname%"

echo ""

###
# Create header and footer menus
###

echo "Creating Menus..."

php wp-cli.phar db query "INSERT INTO \`${PREFIX}_options\` (\`option_name\`, \`option_value\`, \`autoload\`) VALUES ('theme_mods_$THEME', 'a:1:{s:18:\"nav_menu_locations\";a:2:{s:6:\"header\";i:2;s:6:\"footer\";i:3;}}', 'yes')"
php wp-cli.phar db query "INSERT INTO \`${PREFIX}_term_taxonomy\` (\`term_taxonomy_id\`, \`term_id\`, \`taxonomy\`, \`description\`, \`parent\`, \`count\`) VALUES (2, 2, 'nav_menu', '', 0, 0)"
php wp-cli.phar db query "INSERT INTO \`${PREFIX}_term_taxonomy\` (\`term_taxonomy_id\`, \`term_id\`, \`taxonomy\`, \`description\`, \`parent\`, \`count\`) VALUES (3, 3, 'nav_menu', '', 0, 0)"
php wp-cli.phar db query "INSERT INTO \`${PREFIX}_terms\` (\`term_id\`, \`name\`, \`slug\`, \`term_group\`) VALUES (2, 'header', 'header', 0)"
php wp-cli.phar db query "INSERT INTO \`${PREFIX}_terms\` (\`term_id\`, \`name\`, \`slug\`, \`term_group\`) VALUES (3, 'footer', 'footer', 0)"

success "OK"

###
# Close comments and pingback
###

echo "Closing Comments..."

php wp-cli.phar option update "default_comment_status" "closed"
php wp-cli.phar option update "default_ping_status" "closed"
php wp-cli.phar option update "default_pingback_flag" ""
php wp-cli.phar option update "comment_moderation" ""
php wp-cli.phar option update "comment_registration" ""
php wp-cli.phar option update "close_comments_for_old_posts" ""
php wp-cli.phar option update "page_comments" ""

echo ""

###
# Change administrator ID
###

echo "Updating Administrator ID..."

php wp-cli.phar db query "UPDATE \`${PREFIX}_users\` SET \`ID\` = 2 WHERE \`ID\` = 1"
php wp-cli.phar db query "UPDATE \`${PREFIX}_usermeta\` SET \`user_id\` = 2 WHERE \`user_id\` = 1"

success "OK"

###
# Timezone
###

echo "Setting Timezone..."

php wp-cli.phar option update "gmt_offset" ""
php wp-cli.phar option update "timezone_string" $TIMEZONE

echo ""

###
# Media size to 0
###

echo "Resetting Media Size..."

php wp-cli.phar option update "thumbnail_size_w" "0"
php wp-cli.phar option update "thumbnail_size_h" "0"
php wp-cli.phar option update "medium_size_w" "0"
php wp-cli.phar option update "medium_size_h" "0"
php wp-cli.phar option update "large_size_w" "0"
php wp-cli.phar option update "large_size_h" "0"
php wp-cli.phar option update "image_default_link_type" ""

echo ""

###
# Install plugins
###

if [ "$PLUGINS" = "true" ]; then
    echo "Installing Plugins..."
    echo ""

    php wp-cli.phar plugin install wordpress-seo
    echo ""

    php wp-cli.phar plugin install better-wp-security
    echo ""

    php wp-cli.phar plugin install advanced-custom-fields
    echo ""
fi

echo "Deleting Hello Dolly..."
echo ""

php wp-cli.phar plugin uninstall hello
echo ""

###
# Remove temporary files
###

echo "Removing Temporary Files..."

rm -rf $DESTINATION"src/"
rm "README.md" "install.sh" "wp-cli.phar"

success "OK"

###
# Display administrator password
###

section "Done! Your administrator password is: $PASSWORD"
