<?php
/**
 * @category Blogwerk
 * @package Blogwerk_Theme
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */

namespace Blogwerk\Theme;

use \WP;
use \WP_Query;
use \WP_Admin_Bar;
use \WP_Object_Cache;
use \wpdb;
use \WP_Rewrite;
use \WP_Roles;
use \WP_Post;
use \WP_Scripts;
use \WP_Styles;
use \WP_Widget_Factory;

/**
 * Class WordPressGlobalsWrapper
 *
 * This wrapper provides a wrapper to global objects of the WordPress Core.
 *
 * @category Blogwerk
 * @package Blogwerk_Theme
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
class WordPressGlobalsWrapper
{

  /**
   * Getter for global $wp
   *
   * @return WP
   */
  public function getWp()
  {
    global $wp;
    return $wp;
  }

  /**
   * Getter for global $wp_query
   *
   * @return object|WP_Query
   */
  public function getQuery()
  {
    global $wp_query;
    return $wp_query;
  }

  /**
   * Setter for global $wp_query
   *
   * @param WP_Query $query
   */
  public function setQuery($query){
    global $wp_query;
    $wp_query = $query;
  }

  /**
   * Getter for global $wp_admin_bar
   *
   * @return null|WP_Admin_Bar
   */
  public function getAdminBar()
  {
    global $wp_admin_bar;
    return $wp_admin_bar;
  }

  /**
   * Getter for global $wp_object_cache
   *
   * @return WP_Object_Cache
   */
  public function getObjectCache()
  {
    global $wp_object_cache;
    return $wp_object_cache;
  }

  /**
   * Getter for global $pagenow
   *
   * @return string
   */
  public function getPageNow()
  {
    global $pagenow;
    return $pagenow;
  }
  /**
   * Getter for global $typenow
   *
   * @return string
   */
  public function getTypeNow()
  {
    global $typenow;
    return $typenow;
  }

  /**
   * Getter for global $wpdb
   *
   * @return wpdb
   */
  public function getDb()
  {
    global $wpdb;
    return $wpdb;
  }

  /**
   * Getter for global $wp_rewrite
   *
   * @return WP_Rewrite
   */
  public function getRewrite()
  {
    global $wp_rewrite;
    return $wp_rewrite;
  }

  /**
   * Getter for global $wp_roles
   *
   * @return WP_Roles
   */
  public function getRoles()
  {
    global $wp_roles;
    return $wp_roles;
  }

  /**
   * Getter for global $wp_user_roles
   *
   * @return mixed
   */
  public function getUserRoles()
  {
    global $wp_user_roles;
    return $wp_user_roles;
  }

  /**
   * Getter for global $wp_version
   *
   * @return string
   */
  public function getVersion()
  {
    global $wp_version;
    return $wp_version;
  }

  /**
   * Getter for global $post
   *
   * @return null|WP_Post
   */
  public function getPost()
  {
    global $post;
    return $post;
  }

  /**
   * Getter for global $comment
   *
   * @return array|bool|null|object
   */
  public function getComment()
  {
    global $comment;
    return $comment;
  }

  /**
   * Getter for global $comments
   *
   * @return array
   */
  public function getComments()
  {
    global $comments;
    return $comments;
  }

  /**
   * Getter for global $custom_image_header
   *
   * @return mixed
   */
  public function getCustomImageHeader()
  {
    global $custom_image_header;
    return $custom_image_header;
  }

  /**
   * Getter for global $shortcode_tags
   *
   * @return array
   */
  public function getShortcodeTags()
  {
    global $shortcode_tags;
    return $shortcode_tags;
  }

  /**
   * Getter for global $wp_theme_directories
   *
   * @return mixed
   */
  public function getThemeDirectories()
  {
    global $wp_theme_directories;
    return $wp_theme_directories;
  }

  /**
   * Getter for global $wp_themes
   *
   * @return mixed
   */
  public function getThemes()
  {
    global $wp_themes;
    return $wp_themes;
  }

  /**
   * Getter for global $wp_locale
   *
   * @return mixed
   */
  public function getLocale()
  {
    global $wp_locale;
    return $wp_locale;
  }

  /**
   * Getter $menu
   *
   * @return array|bool|mixed|string|void
   */
  public function getMenu()
  {
    global $menu;
    return $menu;
  }

  /**
   * Setter for global $menu
   *
   * @param $newMenu
   */
  public function setMenu($newMenu)
  {
    global $menu;
    $menu = $newMenu;
  }

  /**
   * Getter for global $submenu_file
   *
   * @return string
   */
  public function getSubMenuFile()
  {
    global $submenu_file;
    return $submenu_file;
  }

  /**
   * Getter for global $submenu
   *
   * @return array
   */
  public function getSubMenu()
  {
    global $submenu;
    return $submenu;
  }

  /**
   * Setter for global $submenu
   *
   * @param array $newSubbMenu
   */
  public function setSubMenu($newSubbMenu = array())
  {
    global $submenu;
    $submenu = $newSubbMenu;
  }

  /**
   * Setter for global $submenu_file
   *
   * @param string $file
   */
  public function setSubMenuFile($file)
  {
    global $submenu_file;
    $submenu_file = $file;
  }

  /**
   * Getter for global $wp_scripts
   *
   * @return WP_Scripts
   */
  public function getScripts()
  {
    global $wp_scripts;
    return $wp_scripts;
  }

  /**
   * Getter for global $wp_styles
   *
   * @return WP_Styles
   */
  public function getStyles()
  {
    global $wp_styles;
    return $wp_styles;
  }

  /**
   * Getter for global $l10n
   *
   * @return mixed
   */
  public function getl10n()
  {
    global $l10n;
    return $l10n;
  }

  /**
   * Getter for global $$wp_registered_widgets
   *
   * @return array
   */
  public function getWidgets()
  {
    global $wp_registered_widgets;
    return $wp_registered_widgets;
  }

  /**
   * Getter for global $wp_registered_sidebars
   *
   * @return array
   */
  public function getSidebars()
  {
    global $wp_registered_sidebars;
    return $wp_registered_sidebars;
  }

  /**
   * Getter for global $wp_widget_factory
   *
   * @return WP_Widget_Factory
   */
  public function getWidgetFactory()
  {
    global $wp_widget_factory;
    return $wp_widget_factory;
  }

} 