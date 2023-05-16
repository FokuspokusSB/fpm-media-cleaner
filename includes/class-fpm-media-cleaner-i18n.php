<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://fokuspokus-media.de
 * @since      1.0.0
 *
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/includes
 * @author     Sören <sb@fokuspokus-media.de>
 */
class Fpm_Media_Cleaner_i18n
{
  /**
   * Load the plugin text domain for translation.
   *
   * @since    1.0.0
   */
  public function load_plugin_textdomain()
  {
    load_plugin_textdomain(
      "fpm-media-cleaner",
      false,
      dirname(dirname(plugin_basename(__FILE__))) . "/languages/"
    );
  }
}
