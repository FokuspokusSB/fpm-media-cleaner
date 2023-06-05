<?php

require_once plugin_dir_path(dirname(__FILE__)) .
  "includes/class-fpm-media-cleaner-config.php";
/**
 * Fired during plugin activation
 *
 * @link       https://fokuspokus-media.de
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
    global $wpdb;
    $table_name = $wpdb->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;
    $table_options_name =
      $wpdb->prefix . MEDIA_CLEANER_CONFIG::OPTIONS_TABLE_NAME;
    $table_log_name = $wpdb->prefix . MEDIA_CLEANER_CONFIG::LOG_TABLE_NAME;

    $wpdb->query(
      '
			CREATE TABLE IF NOT EXISTS  `' .
        $table_name .
        '` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
				`post_id` INT NULL ,
				`insert_date` DATETIME NULL,
				PRIMARY KEY (`id`) ,
				UNIQUE INDEX `fpm_id_UNIQUE` (`id` ASC),
				UNIQUE (`post_id`) )
			ENGINE = InnoDB
			DEFAULT CHARACTER SET = utf8
			COLLATE = utf8_unicode_ci;
		'
    );

    $wpdb->query(
      '
			CREATE TABLE IF NOT EXISTS  `' .
        $table_options_name .
        '` (
				`option_key` VARCHAR(255) NOT NULL ,
				`option_value` VARCHAR(255) NULL ,
				PRIMARY KEY (`option_key`) ,
				UNIQUE INDEX `fpm_option_key_UNIQUE` (`option_key` ASC) )
			ENGINE = InnoDB
			DEFAULT CHARACTER SET = utf8
			COLLATE = utf8_unicode_ci;
		'
    );

    $wpdb->query(
      '
			CREATE TABLE IF NOT EXISTS  `' .
        $table_log_name .
        '` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
          `insert_date` DATETIME NULL,
          `status` VARCHAR(255) NULL ,
          `count` VARCHAR(255) NULL ,
          `log` longtext NULL ,
          PRIMARY KEY (`id`) ,
          UNIQUE INDEX `fpm_log_id_UNIQUE` (`id` ASC) 
        )
			ENGINE = InnoDB
			DEFAULT CHARACTER SET = utf8
			COLLATE = utf8_unicode_ci;
		'
    );
    self::_set_default_config();
  }

  private static function _set_default_config()
  {
    self::_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["STATUS"],
      MEDIA_CLEANER_CONFIG::STATUS_VALUES["init"]
    );

    self::_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["LAST_UPDATE"],
      date("c")
    );

    self::_set_option(MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["COUNT"], 0);

    self::_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["SKIP_IDS"],
      json_encode([])
    );

    self::_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["EXTERNAL_PLUGIN_FILEBIRD_IDS"],
      json_encode([])
    );
  }

  private static function _set_option($key, $value)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . MEDIA_CLEANER_CONFIG::OPTIONS_TABLE_NAME;
    $sql = "INSERT INTO {$table_name} (option_key,option_value) VALUES (%s,%s) ON DUPLICATE KEY UPDATE option_value = %s";
    $sql = $wpdb->prepare($sql, $key, $value, $value);
    $wpdb->query($sql);
  }
}
