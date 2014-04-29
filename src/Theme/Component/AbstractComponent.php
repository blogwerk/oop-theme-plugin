<?php
/**
 * @category    Blogwerk
 * @package     Blogwerk_Theme
 * @subpackage  Component
 * @author      Tom Forrer <tom.forrer@blogwerk.com>
 * @copyright   Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */

namespace Blogwerk\Theme\Component;

use Blogwerk\Theme\AbstractWordPressTheme;
use Blogwerk\Util\WordPress;
use \WP_Rewrite;

/**
 * Class AbstractComponent
 *
 * Base class for components
 *
 * @category    Blogwerk
 * @package     Blogwerk_Theme
 * @subpackage  Component
 * @author      Tom Forrer <tom.forrer@blogwerk.com>
 * @copyright   Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
abstract class AbstractComponent
{
  /**
   * @var AbstractWordPressTheme the theme instance
   */
  protected $theme;

  /**
   * Creates the Component and registers the init() call at action "init"
   *
   * @param AbstractWordPressTheme $theme the theme
   */
  public function __construct(AbstractWordPressTheme $theme)
  {
    $this->theme = $theme;
    add_action('init', array($this, 'init'));
    add_action('wp_enqueue_scripts', array($this, 'assets'));
    if (is_admin()) {
      add_action('admin_enqueue_scripts', array($this, 'adminAssets'));
      add_action('admin_init', array($this, 'admin'));
    }
  }

  /**
   * Needs to be implemented by the Component to initialize itself
   */
  abstract public function init();

  /**
   * Empty wp_enqueue_scripts callback. Can be overridden.
   */
  public function assets()
  {
    // register styles and scripts. override in specific Component implementation
  }

  /**
   * Empty after_setup_theme callback. Can be overriden.
   */
  public function setup()
  {
    // register early stuff. override in specific Component implementation
  }

  /**
   * Empty admin_init(10) callback. Can be overridden.
   */
  public function admin()
  {
    // register admin hooks. override in specific Component implementation
  }

  /**
   * Empty admin_enqueue_scripts(10) callback. Can be overridden.
   */
  public function adminAssets()
  {
    // register admin styles and scripts. override in specific Component implementation
  }

  /**
   * Registering a post type
   *
   * @param string $type slug of the type
   * @param string $singular singular name
   * @param string $plural plural name
   * @param array $config can override the defaults of this function (array_merge)
   */
  protected function registerPostType($type, $singular, $plural, $config = array())
  {
    WordPress::registerPostType($type, $singular, $plural, $config);
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
  protected function registerTaxonomy($slug, $singular, $plural, $letter = '', $config = array(), $types = array('post'))
  {
    WordPress::registerTaxonomy($slug, $singular, $plural, $letter, $config, $types);
  }

  /**
   * Generates all the rewrite rules for a given post type.
   *
   * The rewrite rules allow a post type to be filtered by all possible combinations & permutations
   * of taxonomies that apply to the specified post type and additional query_vars specified with
   * the $queryVars parameter.
   *
   * Must be called from a function hooked to the 'generate_rewrite_rules' action so that the global
   * $wpRewrite->preg_index function returns the correct value.
   *
   * You have to use pre_get_posts, query_vars filters to add the category/tag rewrites correctly.
   * See for example ~/kpt-intranet-blog/src/KptIntranetBlog/Theme/Component/UserImageComponent.php
   *
   * @param \WP_Rewrite $wpRewrite
   * @param string|object $postType The post type for which you wish to create the rewrite rules
   * @param array $queryVars optional Non-taxonomy query vars you wish to create rewrite rules for. Rules will be created to capture any single string for the query_var, that is, a rule of the form '/query_var/(.+)/'
   *
   * @author Brent Shepherd <me@brentshepherd.com>
   * @since 1.0
   * @url http://thereforei.am/2011/10/28/advanced-taxonomy-queries-with-pretty-urls/
   */

  function generateRewriteRulesForPostType($wpRewrite, $postType, $queryVars = array())
  {
    if (!is_object($postType)) {
      $postType = get_post_type_object($postType);
    }

    $newRewriteRules = array();

    $taxonomies = get_object_taxonomies($postType->name, 'objects');

    // Add taxonomy filters to the query vars array
    foreach ($taxonomies as $taxonomy) {
      if ($taxonomy->query_var == 'category_name' || $taxonomy->query_var == 'post_tag') {
        $queryVars[] = $taxonomy->rewrite['slug'];
      } else {
        $queryVars[] = $taxonomy->query_var;
      }
    }

    // Loop over all the possible combinations of the query vars
    for ($i = 1; $i <= count($queryVars); $i++) {

      $newRewriteRule = $postType->rewrite['slug'] . '/';
      $newQueryString = 'index.php?post_type=' . $postType->name;

      // Prepend the rewrites & queries
      for ($n = 1; $n <= $i; $n++) {
        $newRewriteRule .= '(' . implode('|', $queryVars) . ')/(.+?)/';
        $newQueryString .= '&' . $wpRewrite->preg_index($n * 2 - 1) . '=' . $wpRewrite->preg_index($n * 2);
      }

      // Allow paging of filtered post type - WordPress expects 'page' in the URL but uses 'paged' in the query string so paging doesn't fit into our regex
      $newPagedRewriteRule = $newRewriteRule . 'page/([0-9]{1,})/';
      $newPagedQueryString = $newQueryString . '&paged=' . $wpRewrite->preg_index($i * 2 + 1);

      // Make the trailing backslash optional
      $newPagedRewriteRule = $newPagedRewriteRule . '?$';
      $newRewriteRule = $newRewriteRule . '?$';

      // Add the new rewrites
      $newRewriteRules = array(
          $newPagedRewriteRule => $newPagedQueryString,
          $newRewriteRule => $newQueryString
        ) + $newRewriteRules;
    }

    $wpRewrite->rules = $newRewriteRules + $wpRewrite->rules;
  }

  /**
   * @return \Blogwerk\Theme\AbstractWordPressTheme
   */
  public function getTheme()
  {
    return $this->theme;
  }
}