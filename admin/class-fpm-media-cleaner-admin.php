<?php

require_once plugin_dir_path(dirname(__FILE__)) .
  "includes/class-fpm-media-cleaner-config.php";
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://samyblake.ninja
 * @since      1.0.0
 *
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/admin
 * @author     Sören <sb@fokuspokus-media.de>
 */
class Fpm_Media_Cleaner_Admin
{
  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct($plugin_name, $version)
  {
    global $wpdb;
    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->db = $wpdb;

    add_action("admin_menu", [$this, "admin_menu"]);

    add_action("wp_ajax_media-clean-fill-cache", [$this, "action_fill_cache"]);
    add_action("wp_ajax_media-clean-get-cache", [$this, "action_get_cache"]);
  }

  public function action_fill_cache()
  {
    $this->_refill_cache_table();
    wp_die();
  }

  public function action_get_cache()
  {
    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;
    $post_table_name = $this->db->prefix . "posts";
    $SQL =
      '
      SELECT fpm.id, p.post_title, p.post_type, p.post_status, p.post_modified
      FROM `' .
      $table_name .
      '` as fpm
      INNER JOIN `' .
      $post_table_name .
      '` as p ON fpm.ID = p.ID
    ';

    echo json_encode($not_direct_link);

    wp_die();
  }

  private function _refill_cache_table()
  {
    $SQL_NOT_DIRECT_LINKED = "
    SELECT p.ID, p.guid
    FROM wp_posts as p
    WHERE p.post_type = 'attachment'
    AND p.ID not in (
      SELECT p.ID
      FROM wp_posts as p
      INNER JOIN wp_postmeta as pm
      ON p.ID = pm.meta_value
      WHERE p.post_type = 'attachment'
    )
    AND p.ID not in (
      SELECT p.ID
      FROM wp_posts as p
      INNER JOIN wp_options as po
      ON p.ID = po.option_value
      WHERE p.post_type = 'attachment'
    )";

    $attachments_not_direct_link = $this->db->get_results(
      $SQL_NOT_DIRECT_LINKED,
      ARRAY_A
    );
    $attachment_not_link = [];
    $place_holders = [];

    foreach ($attachments_not_direct_link as $key => $attachment) {
      $SQL_SEARCH_IN_POST_CONTENT = "
        SELECT post_content 
        FROM wp_posts 
        WHERE option_value like '%{$attachment["ID"]}%'
      ";
      $SQL_SEARCH_IN_OPTION_CONTENT = " 
        SELECT post_content 
        FROM wp_posts
        WHERE post_content like '%{$attachment["ID"]}%'
      ";
      $post_content_result = $this->db->get_results(
        $SQL_SEARCH_IN_POST_CONTENT
      );
      $options_content_result = $this->db->get_results(
        $SQL_SEARCH_IN_OPTION_CONTENT
      );

      if (
        sizeof($post_content_result) == 0 &&
        sizeof($options_content_result) == 0
      ) {
        // is not in post_content
        $attachment["guid"] = str_replace(
          "http://kinedo.local/wp-content/uploads/",
          "",
          $attachment["guid"]
        );
        $attachment_not_link[] = [
          "post_id" => $attachment["ID"],
          "insert_date" => date("Y-m-d H:i:s"),
        ];
        $place_holders[] = "('%d', '%s')";
      }
    }

    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;
    $query = "INSERT INTO `{$table_name}` (post_id, insert_date) VALUES ";

    $query .= implode(", ", $place_holders);
    $wpdb->query($wpdb->prepare("$query ", $attachment_not_link));
  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles()
  {
    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Fpm_Media_Cleaner_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Fpm_Media_Cleaner_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */

    wp_enqueue_style(
      $this->plugin_name,
      plugin_dir_url(__FILE__) . "css/fpm-media-cleaner-admin.css",
      [],
      $this->version,
      "all"
    );
  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts()
  {
    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Fpm_Media_Cleaner_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Fpm_Media_Cleaner_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */

    wp_enqueue_script(
      $this->plugin_name,
      plugin_dir_url(__FILE__) . "js/fpm-media-cleaner-admin.js",
      ["jquery"],
      $this->version,
      false
    );
  }

  public function admin_menu()
  {
    add_menu_page(
      "FPM Media Cleaner",
      "FPM Media Cleaner",
      "publish_pages",
      "fpm-media-cleaner",
      [$this, "admin_page"]
    );
  }

  public function admin_page()
  {
    require_once plugin_dir_path(dirname(__FILE__)) .
      "admin/partials/fpm-media-cleaner-admin-display.php";
  }
}
