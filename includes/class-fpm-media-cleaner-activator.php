<?php

/**
 * Fired during plugin activation
 *
 * @link       https://samyblake.ninja
 * @since      1.0.0
 *
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/includes
 * @author     Sören <sb@fokuspokus-media.de>
 */
class Fpm_Media_Cleaner_Activator
{
  /**
   * Short Description. (use period)
   *
   * Long Description.
   *
   * @since    1.0.0
   */
  public static function activate()
  {
    require_once plugin_dir_path(dirname(__FILE__)) .
      "includes/class-fpm-media-cleaner-config.php";

    global $wpdb;
    $table_name = $wpdb->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;

    $wpdb->query(
      '
			CREATE TABLE IF NOT EXISTS  `' .
        $table_name .
        '` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
				`post_id` INT NULL ,
				`insert_date` DATETIME NULL,
				PRIMARY KEY (`id`) ,
				UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
			ENGINE = InnoDB
			DEFAULT CHARACTER SET = utf8
			COLLATE = utf8_unicode_ci;
		'
    );
  }
}
