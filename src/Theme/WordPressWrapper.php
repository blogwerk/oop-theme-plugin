<?php
/**
 * Class WordPressWrapper
 *
 * This wrapper provides a wrapper to global objects of the WordPress Core.
 *
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
 * Class SocialMediaKitWrapper
 *
 * This wrapper provides a wrapper to global objects of the WordPress Core.
 *
 * @category Blogwerk
 * @package Blogwerk_Theme
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
class WordPressWrapper
{

  /**
   * @var WP $wp
   */
  protected $wp;

  /**
   * @var WP_Query $query
   */
  protected $query;

  /**
   * @var WP_Admin_Bar $query
   */
  protected $adminBar;

  /**
   * @var WP_Object_Cache $objectCache
   */
  protected $objectCache;

  /**
   * @var string $pagenow
   */
  protected $pagenow;

  /**
   * @var string $typenow
   */
  protected $typenow;

  /**
   * @var wpdb $db
   */
  protected $db;

  /**
   * @var WP_Rewrite $rewrite
   */
  protected $rewrite;

  /**
   * @var WP_Roles $roles
   */
  protected $roles;

  /**
   * @var mixed $userRoles
   */
  protected $userRoles;

  /**
   * @var WP_Post|null $post
   */
  protected $post;

  /**
   * @var array|bool|null|object $comment
   */
  protected $comment;

  /**
   * @var array $comments
   */
  protected $comments;

  /**
   * @var array $menu
   */
  protected $menu;

  /**
   * @var array $subMenu
   */
  protected $subMenu;

  /**
   * @var string $subMenuFile
   */
  protected $subMenuFile;

  /**
   * @var WP_Widget_Factory $widetFactory
   */
  protected $widgetFactory;

  /**
   * Helper function, if no Multilang Plugin is loaded
   *
   * @return array
   */
  public function getAllLanguages()
  {
    return array('de');
  }

  /**
   * @return WP
   */
  public function getWp()
  {
    global $wp;
    $this->wp = $wp;
    return $this->wp;
  }

  /**
   * Getter
   *
   * @return object|WP_Query
   */
  public function getQuery()
  {
    global $wp_query;
    $this->query = $wp_query;
    return $wp_query;
  }

  /**
   * Setter
   *
   * @param WP_Query $query
   */
  public function setQuery($query){
    global $wp_query;
    $wp_query = $query;
    $this->query = $wp_query;
  }

  /**
   * @return null|WP_Admin_Bar
   */
  public function getAdminBar()
  {
    global $wp_admin_bar;
    $this->adminBar = $wp_admin_bar;
    return $wp_admin_bar;
  }

  /**
   * @return WP_Object_Cache
   */
  public function getObjectCache()
  {
    global $wp_object_cache;
    $this->objectCache = $wp_object_cache;
    return $wp_object_cache;
  }

  /**
   * @return string
   */
  public function getPageNow()
  {
    global $pagenow;
    $this->pagenow = $pagenow;
    return $pagenow;
  }
  /**
   * @return string
   */
  public function getTypeNow()
  {
    global $typenow;
    $this->typenow = $typenow;
    return $typenow;
  }

  /**
   * @return wpdb
   */
  public function getDb()
  {
    global $wpdb;
    $this->db = $wpdb;;
    return $wpdb;
  }

  /**
   * @return WP_Rewrite
   */
  public function getRewrite()
  {
    global $wp_rewrite;
    $this->rewrite = $wp_rewrite;
    return $wp_rewrite;
  }

  /**
   * @return WP_Roles
   */
  public function getRoles()
  {
    global $wp_roles;
    $this->roles = $wp_roles;
    return $wp_roles;
  }

  /**
   * @return mixed
   */
  public function getUserRoles()
  {
    global $wp_user_roles;
    $this->userRoles = $wp_user_roles;
    return $wp_user_roles;
  }

  /**
   * @return string
   */
  public function getVersion()
  {
    global $wp_version;
    return $wp_version;
  }

  /**
   * @return null|WP_Post
   */
  public function getPost()
  {
    global $post;
    $this->post = $post;
    return $post;
  }

  /**
   * @return array|bool|null|object
   */
  public function getComment()
  {
    global $comment;
    $this->comment = $comment;
    return $comment;
  }

  /**
   * @return array
   */
  public function getComments()
  {
    global $comments;
    $this->comments = $comments;
    return $comments;
  }

  /**
   * @return mixed
   */
  public function getCustomImageHeader()
  {
    global $custom_image_header;
    return $custom_image_header;
  }

  /**
   * @return array
   */
  public function getShortcodeTags()
  {
    global $shortcode_tags;
    return $shortcode_tags;
  }

  /**
   * @return mixed
   */
  public function getThemeDirectories()
  {
    global $wp_theme_directories;
    return $wp_theme_directories;
  }

  public function getThemes()
  {
    global $wp_themes;
    return $wp_themes;
  }

  /**
   * @return mixed
   */
  public function getLocale()
  {
    global $wp_locale;
    return $wp_locale;
  }

  /**
   * @return array|bool|mixed|string|void
   */
  public function getMenu()
  {
    global $menu;
    $this->menu = $menu;
    return $menu;
  }

  public function setMenu($newMenu)
  {
    global $menu;
    $this->menu = $newMenu;
    $menu = $newMenu;
  }

  /**
   * @return string
   */
  public function getSubMenuFile()
  {
    global $submenu_file;
    $this->subMenuFile = $submenu_file;
    return $submenu_file;
  }

  /**
   * @return array
   */
  public function getSubMenu()
  {
    global $submenu;
    $this->subMenu = $submenu;
    return $submenu;
  }

  /**
   * @param array $newSubbMenu
   */
  public function setSubMenu($newSubbMenu = array())
  {
    global $submenu;
    $this->subMenu = $newSubbMenu;
    $submenu = $newSubbMenu;
  }

  /**
   * @param string $file
   */
  public function setSubMenuFile($file)
  {
    global $submenu_file;
    $submenu_file = $file;
  }

  /**
   * @return WP_Scripts
   */
  public function getScripts()
  {
    global $wp_scripts;
    return $wp_scripts;
  }

  /**
   * @return WP_Styles
   */
  public function getStyles()
  {
    global $wp_styles;
    return $wp_styles;
  }

  /**
   * @return mixed
   */
  public function getl10n()
  {
    global $l10n;
    return $l10n;
  }

  /**
   * @return array
   */
  public function getWidgets()
  {
    global $wp_registered_widgets;
    return $wp_registered_widgets;
  }

  /**
   * @return array
   */
  public function getSidebars()
  {
    global $wp_registered_sidebars;
    return $wp_registered_sidebars;
  }

  /**
   * @return \WP_Widget_Factory
   */
  public function getWidgetFactory()
  {
    global $wp_widget_factory;
    return $wp_widget_factory;
  }

} 