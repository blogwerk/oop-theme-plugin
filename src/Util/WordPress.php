<?php
/**
 * @package Blogwerk_Util
 * @author Michael Sebel <michael.sebel@blogwerk.com>
 * @copyright Blogwerk AG
 */

namespace Blogwerk\Util;

/**
 * Utility functions to wrap wordpress core functions or make the handling of them easier
 *
 * @package Blogwerk_Util
 * @author Michael Sebel <michael.sebel@blogwerk.com>
 * @copyright Blogwerk AG
 */
class WordPress
{
  /**
   * Registering a post type
   *
   * @param string $type slug of the type
   * @param string $singular singular name
   * @param string $plural plural name
   * @param array $config can override the defaults of this function (array_merge)
   */
  public static function registerPostType($type, $singular, $plural, $config = array())
  {
    $labels = array(
      'name' => $plural,
      'singular_name' => $singular,
      'add_new' => 'Erstellen',
      'add_new_item' => $singular . ' erfassen',
      'edit_item' => 'Bearbeite ' . $singular,
      'new_item' => 'Neues ' . $singular,
      'view_item' => $singular . ' ansehen',
      'search_items' => $singular . ' suchen',
      'not_found' => 'Keine ' . $plural . ' gefunden',
      'not_found_in_trash' => 'Keine ' . $plural . ' im Papierkorb gefunden',
      'parent_item_colon' => ''
    );

    $defaults = array(
      'labels' => $labels,
      'public' => true,
      'has_archive' => true,
      'plural_view_adminbar' => false,
    );

    $arguments = array_merge_recursive_distinct($defaults, $config);

    if (isset($arguments['plural_view_adminbar']) && $arguments['plural_view_adminbar']) {
      add_action('admin_bar_menu', function ($wp_admin_bar) use ($type, $arguments) {
        $object = get_queried_object();
        if ($object->name == $type) {
          $title = $object->labels->menu_name;
          if (is_admin()) {
            $url = get_post_type_archive_link($type);
          } else {
            $url = get_admin_url(null, 'edit.php?post_type=' . $type);
          }
        }
        if ($object->post_type == $type) {
          if (!is_admin()) {
            $url = get_edit_post_link($object->ID);
            $title = $arguments['labels']['edit_item'];
          }
        }

        // Add admin bar entry
        if ($url) {
          $wp_admin_bar->add_node(array(
            'id' => 'custom-button',
            'title' => $title,
            'href' => $url,
          ));
        }
      }, 95);
    }

    register_post_type($type, $arguments);
  }

  /**
   * Registers a taxonomy
   *
   * @param string $slug the slug of the taxonomy
   * @param string $singular singular name
   * @param string $plural plural name
   * @param string $letter letter after "Übergeordnete" and "Neue" -> Could be "n" or "s"
   * @param array $config override the configuration with this array
   * @param array $types the types to be assigned (defaults to array("post"))
   */
  public static function registerTaxonomy($slug, $singular, $plural, $letter = '', $config = array(), $types = array('post'))
  {
    if (!is_array($types)) {
      $types = array($types);
    }

    $labels = array(
      'name' => $singular,
      'singular_name' => $singular,
      'search_items' => $plural . ' suchen',
      'popular_items' => '',
      'all_items' => 'Alle ' . $plural,
      'view_item' => $singular . ' ansehen',
      'parent_item' => 'Übergeordnete' . $letter . ' ' . $singular,
      'parent_item_colon' => 'Übergeordnete' . $letter . ' ' . $singular . ':',
      'edit_item' => $singular . ' bearbeiten',
      'update_item' => $singular . ' speichern',
      'add_new_item' => 'Neue' . $letter . ' ' . $singular . ' hinzufügen',
      'new_item_name' => 'Neue' . $letter . ' ' . $singular,
      'separate_items_with_commas' => $plural . ' durch Komma trennen',
      'add_or_remove_items' => $plural . ' hinzufügen oder entfernen',
      'menu_name' => $plural
    );

    $defaults = array(
      'hierarchical' => true,
      'public' => true,
      'labels' => $labels,
      'also_show_in_menu' => false,
      'submenu_page_url' => 'edit-tags.php?taxonomy=' . $slug,
      'submenu_priority' => 10
    );

    $arguments = array_merge_recursive_distinct($defaults, $config);

    if ($arguments['also_show_in_menu'] !== false) {
      add_action('admin_menu', function () use ($slug, $arguments, $types) {
        add_submenu_page(
          $arguments['also_show_in_menu'],
          $arguments['labels']['name'],
          $arguments['labels']['menu_name'],
          'manage_categories',
          $arguments['submenu_page_url']
        );
      }, $arguments['submenu_priority']);
      // show submenu entry in 'show_in_menu' menu
      add_action('parent_file', function ($parentFile) use ($arguments, $types) {
        if ($parentFile == 'edit.php?post_type=' . $types[0]) {
          $parentFile = $arguments['also_show_in_menu'];
        }
        return $parentFile;
      });
    }

    register_taxonomy($slug, $types, $arguments);

    // make sure it works as suggested by codex (http://codex.wordpress.org/Function_Reference/register_taxonomy#Usage "better be safe than sorry")
    foreach ($types as $type) {
      register_taxonomy_for_object_type($slug, $type);
    }
  }

  /**
   * @param string $fileId id from $_FILES
   * @param bool $validateImage makes sure, it's an image
   * @return int attachment id of the uploaded item
   */
  public static function uploadAttachment($fileId, $validateImage)
  {
    if (!function_exists('wp_generate_attachment_metadata')){
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');
    }

    // Check for images, if needed, and return 0 if no image
    if ($validateImage) {
      $file = $_FILES[$fileId];
      if (!File::isImage($file['name']) || !File::isImageMime($file['type'])) {
        return 0;
      }
    }

    // Run the update
    $result = media_handle_upload($fileId, 0);

    // Check for errors
    if ($result instanceof WP_Error) {
      return 0;
    }

    return intval($result);
  }

  /**
   * Sets correct heads for json output
   * @param array $result the array that should be send via json
   */
  public static function sendJsonResponse($result)
  {
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
  }

  /**
   * Caching wrapper for wpNavMenu
   * @param array $config the menu config
   * @param int $cacheTime the cache time
   * @return string html code of the menu
   */
  public static function wpNavMenu($config, $cacheTime = 300)
  {
    // Try to get the menu from cache
    $key = $config['theme_location'] . '_' . md5(json_encode($config));
    $html = wp_cache_get($key, 'wpNavMenu');

    if ($html !== false) {
      return $html;
    }

    // Not from cache, generate it
    $config['echo'] = 0;
    $html = wp_nav_menu($config);
    wp_cache_set($key, $html, 'wpNavMenu', $cacheTime);
    return $html;
  }
}