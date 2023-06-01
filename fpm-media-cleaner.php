<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://fokuspokus-media.de
 * @since             1.0.3
 * @package           Fpm_Media_Cleaner
 *
 * @wordpress-plugin
 * Plugin Name:       Media Cleaner
 * Plugin URI:        https://fokuspokus-media.de
 * Description:       Removed unnecessary Media Files
 * Version:           1.0.3
 * Author:            Sören Balke
 * Author URI:        https://fokuspokus-media.de
 * License:           Copyright
 * Text Domain:       fpm-media-cleaner
 * Domain Path:       /languages
 */

define("FPM_MEDIA_CLEANER_ROOT_DIR", dirname(__FILE__) . "/");

// If this file is called directly, abort.
if (!defined("WPINC")) {
  die();
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define("FPM_MEDIA_CLEANER_VERSION", "1.0.3");

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fpm-media-cleaner-activator.php
 */
function activate_fpm_media_cleaner()
{
  require_once plugin_dir_path(__FILE__) .
    "includes/class-fpm-media-cleaner-activator.php";
  Fpm_Media_Cleaner_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fpm-media-cleaner-deactivator.php
 */
function deactivate_fpm_media_cleaner()
{
  require_once plugin_dir_path(__FILE__) .
    "includes/class-fpm-media-cleaner-deactivator.php";
  Fpm_Media_Cleaner_Deactivator::deactivate();
}

register_activation_hook(__FILE__, "activate_fpm_media_cleaner");
register_deactivation_hook(__FILE__, "deactivate_fpm_media_cleaner");

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . "includes/class-fpm-media-cleaner.php";

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fpm_media_cleaner()
{
  $plugin = new Fpm_Media_Cleaner();
  $plugin->run();
}
run_fpm_media_cleaner();
