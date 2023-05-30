<?php

require_once plugin_dir_path(dirname(__FILE__)) .
  "includes/class-fpm-media-cleaner-config.php";

function buildTree(array $elements, $parentId = 0)
{
  $branch = [];

  foreach ($elements as $element) {
    if ((string) $element["parent"] === (string) $parentId) {
      $children = buildTree($elements, $element["id"]);
      if ($children) {
        $element["children"] = $children;
      }
      $branch[] = $element;
    }
  }

  return $branch;
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://fokuspokus-media.de
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
    add_action("wp_ajax_media-clean-remove", [$this, "action_remove"]);
    add_action("wp_ajax_media-clean-get-cache", [$this, "action_get_cache"]);
    add_action("wp_ajax_media-clean-get-count", [$this, "action_get_count"]);
    add_action("wp_ajax_media-clean-set-skip", [$this, "action_set_skip"]);
    add_action("wp_ajax_media-clean-get-options", [
      $this,
      "action_get_options",
    ]);
    add_action("wp_ajax_media-clean-get-filebird-folders", [
      $this,
      "action_get_filebird_folders",
    ]);
  }

  private function _set_option($key, $value)
  {
    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::OPTIONS_TABLE_NAME;
    $sql = "INSERT INTO {$table_name} (option_key,option_value) VALUES (%s,%s) ON DUPLICATE KEY UPDATE option_value = %s";
    $sql = $this->db->prepare($sql, $key, $value, $value);
    $this->db->query($sql);
  }

  private function _get_options()
  {
    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::OPTIONS_TABLE_NAME;
    $sql = "SELECT * FROM {$table_name}";
    $sql = $this->db->prepare($sql);
    $results = $this->db->get_results($sql);
    return $results;
  }

  public function action_get_filebird_folders()
  {
    $fbv_table_name = $this->db->prefix . "fbv";
    $SQL =
      '
      SELECT *
      FROM `' .
      $fbv_table_name .
      '`
    ';
    $query_result = $this->db->get_results($SQL, ARRAY_A);
    $result = buildTree($query_result);

    echo json_encode($result);
    wp_die();
  }

  public function action_set_skip()
  {
    $skip_ids = $_POST["ids"];
    if (!$skip_ids) {
      wp_die();
      return;
    }

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["SKIP_IDS"],
      json_encode($skip_ids)
    );
    wp_die();
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
      SELECT fpm.id, fpm.post_id, p.post_title, p.post_type, p.post_status, p.post_modified, p.guid
      FROM `' .
      $table_name .
      '` as fpm
      LEFT JOIN `' .
      $post_table_name .
      '` as p ON fpm.post_id = p.ID
      ORDER BY fpm.ID
      LIMIT 100
    ';
    $result = $this->db->get_results($SQL, ARRAY_A);

    for ($i = 0; $i < sizeof($result); $i++) {
      $result[$i]["img"] = wp_get_attachment_image_src($result[$i]["post_id"]);
    }

    echo json_encode($result);

    wp_die();
  }

  public function action_get_options()
  {
    $options = $this->_get_options();

    $skip_ids_index = false;
    foreach ($options as $i => $option) {
      if (
        $option->option_key == MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["SKIP_IDS"]
      ) {
        $skip_ids_index = $i;
        break;
      }
    }

    // get skip_ids media infos
    if ($skip_ids_index) {
      $options[$skip_ids_index]->option_value = json_decode(
        $options[$skip_ids_index]->option_value
      );
      if (is_array($options[$skip_ids_index]->option_value)) {
        for (
          $i = 0;
          $i < sizeof($options[$skip_ids_index]->option_value);
          $i++
        ) {
          $options[$skip_ids_index]->option_value[
            $i
          ] = wp_get_attachment_image_src(
            $options[$skip_ids_index]->option_value[$i]
          );
        }
      }
    }

    echo json_encode($options);
    wp_die();
  }

  public function action_remove()
  {
    $this->_media_remove();
  }

  public function action_get_count()
  {
    $result = [
      "count" => $this->_get_count(),
    ];
    echo json_encode($result);
    wp_die();
  }

  private function _media_remove()
  {
    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["STATUS"],
      "process-remove"
    );
    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["LAST_UPDATE"],
      date("c")
    );
    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;
    $post_table_name = $this->db->prefix . "posts";
    $SQL =
      '
      SELECT id, post_id
      FROM `' .
      $table_name .
      '`
    ';
    $result = $this->db->get_results($SQL, ARRAY_A);
    foreach ($result as $row) {
      wp_delete_attachment($row["post_id"]);
      $this->db->delete($table_name, ["id" => $row["id"]]);
    }

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["STATUS"],
      "finish-remove"
    );
    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["LAST_UPDATE"],
      date("c")
    );

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["COUNT"],
      $this->_get_count()
    );
  }

  private function _get_count()
  {
    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;

    $SQL =
      '
      SELECT COUNT(*) as count
      FROM `' .
      $table_name .
      '`
    ';
    $count = $this->db->get_row($SQL, ARRAY_A);
    return $count["count"] ?: "0";
  }

  private function _refill_cache_table()
  {
    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["STATUS"],
      "process-cache"
    );
    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["LAST_UPDATE"],
      date("c")
    );
    $this->_set_option(MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["COUNT"], "0");

    $options = $this->_get_options();
    $skip_ids = false;
    if ($options[MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["SKIP_IDS"]]) {
      $skip_ids = json_encode(
        $options[MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["SKIP_IDS"]]
      );
      if (!is_array($skip_ids)) {
        $skip_ids = false;
      }
    }

    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;

    $TRUNCATE_SQL =
      '
      TRUNCATE TABLE `' .
      $table_name .
      '`
    ';
    $this->db->query($TRUNCATE_SQL);

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
      if ($skip_ids && in_array($attachment["ID"], $skip_ids)) {
        continue;
      }
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
        // $attachment["guid"] = str_replace(
        //   "http://kinedo.local/wp-content/uploads/",
        //   "",
        //   $attachment["guid"]
        // );

        $this->db->insert($table_name, [
          "post_id" => $attachment["ID"],
          "insert_date" => date("Y-m-d H:i:s"),
        ]);
      }
    }

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["STATUS"],
      "finish-cache"
    );

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["LAST_UPDATE"],
      date("c")
    );

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["COUNT"],
      $this->_get_count()
    );
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
    wp_enqueue_media();

    wp_enqueue_script(
      $this->plugin_name,
      plugin_dir_url(__FILE__) . "js/fpm-media-cleaner-admin.js",
      ["jquery", "jquery-ui-dialog"],
      $this->version,
      false
    );

    wp_enqueue_style("wp-jquery-ui-dialog");
  }

  public function admin_menu()
  {
    add_menu_page(
      "FPM Media Cleaner",
      "FPM Media Cleaner",
      "publish_pages",
      "fpm-media-cleaner",
      [$this, "admin_page"],
      "dashicons-trash"
    );
  }

  public function admin_page()
  {
    $activate_plugins = (array) get_option("active_plugins", []);

    $external_plugins = [
      "filebird" => false,
    ];
    foreach ($activate_plugins as $value) {
      if (str_contains($value, "filebird")) {
        $external_plugins["filebird"] = true;
      }
    }

    require_once plugin_dir_path(dirname(__FILE__)) .
      "admin/partials/fpm-media-cleaner-admin-display.php";
  }
}
