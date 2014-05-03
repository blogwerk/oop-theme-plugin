<?php
/**
 * @category Blogwerk
 * @package Blogwerk_Plugin
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */

namespace Blogwerk\Theme;

/**
 * Class AbstractTwigTheme
 *
 * Extend the AbstractTheme to render Twig template files instead of just plain PHP files
 *
 * @category Blogwerk
 * @package Blogwerk_Plugin
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
abstract class AbstractTwigTheme extends AbstractTheme
{

  /**
   * renders the actual configured twig file for a view or template part
   *
   * @param string $slug the view slug
   * @param mixed $arguments additional argument from get_template_part ($name)
   * @return bool always false, to preview the wordpress loader include
   */
  public function renderView($slug, $arguments = null)
  {
    $viewFile = $this->getViewFileBySlug($slug);

    if ($viewFile) {
      $twig = $this->getService(ServiceContainer::TWIG);
      $template = $twig->loadTemplate($viewFile);
      echo $template->render($this->getViewData($slug, $arguments));
    }

    // override WPINC/template-loader.php to not additionally include something
    return false;
  }

  /**
   * get the view file by slug
   *
   * @param string $slug the view slug
   * @return bool|string view file path if it exists, false otherwise
   */
  public function getViewFileBySlug($slug)
  {
    $viewFile = false;
    $viewsBySlug = $this->getViewsBySlug();
    if (isset($viewsBySlug[$slug])) {
      $viewFile = $viewsBySlug[$slug];
    }
    return $viewFile;
  }

  /**
   * @param $view
   * @param $arguments
   * @return array
   */
  public function getViewData($view, $arguments = null)
  {
    $data = array();
    switch ($view) {
      case 'home':
      case 'index':
      case 'frontpage':
      case 'archive':
      case 'search':
        $data['posts'] = $this->preparePosts();
        break;
      case 'single':
        $data['post'] = $this->preparePost();
    }

    if (is_array($arguments)) {
      $data = array_merge($data, $arguments);
    }
    return $data;
  }

  /**
   * @return array
   */
  public function preparePosts()
  {
    $posts = array();

    while (have_posts()) {
      the_post();
      $post['post_title'] = get_the_title();
      $post['ID'] = get_the_ID();
      $post['permalink'] = get_permalink();
      $post['post_content'] = get_the_content();
      $post['post_excerpt'] = get_the_excerpt();
      $post['comment_count'] = get_comments_number();
      $post['post_date'] = get_the_time('F jS, Y');
      $posts[] = $post;
    }
    return $posts;
  }

  /**
   * @return mixed
   */
  public function preparePost()
  {
    $postId = get_the_ID();
    $post = get_post($postId);
    $post->comments = get_comments(array('post_id' => $postId, 'status' => 'approved'));
    return $post;
  }
} 