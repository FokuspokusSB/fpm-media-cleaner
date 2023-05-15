<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://samyblake.ninja
 * @since      1.0.0
 *
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/includes
 * @author     Sören <sb@fokuspokus-media.de>
 */
class Fpm_Media_Cleaner_Deactivator
{
  /**
   * Short Description. (use period)
   *
   * Long Description.
   *
   * @since    1.0.0
   */
  public static function deactivate()
  {
    global $wpdb;
    require_once plugin_dir_path(dirname(__FILE__)) .
      "includes/class-fpm-media-cleaner-config.php";

    $table_name = $wpdb->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;
    $table_options_name =
      $wpdb->prefix . MEDIA_CLEANER_CONFIG::OPTIONS_TABLE_NAME;

    $wpdb->query(
      '
			TRUNCATE TABLE `' .
        $table_name .
        "`"
    );
    $wpdb->query(
      '
			TRUNCATE TABLE `' .
        $table_options_name .
        "`"
    );
  }
}
