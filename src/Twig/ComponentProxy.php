<?php
/**
 * @category Blogwerk
 * @package Blogwerk_Theme
 * @subpackage Twig
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */

namespace Blogwerk\Twig;

use Blogwerk\Theme\AbstractTheme;

/**
 * Class ComponentProxy
 *
 * @category Blogwerk
 * @package Blogwerk_Theme
 * @subpackage Twig
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 *
 */
class ComponentProxy
{

  /**
   * @var AbstractTheme $theme
   */
  protected $theme;
  public function __construct(AbstractTheme $theme){
    $this->theme = $theme;
  }
  /**
   * Call a non-existent method on the instance of this class:
   * act as a proxy to the function residing in the global namespace.
   *
   * @param string $function the function name
   * @param mixed $arguments function arguments
   * @return mixed|string if the function outputs something, return empty string, otherwise return the function result
   */
  public function __call($function, $arguments)
  {

    $component = $this->theme->getComponent($function);

    return $component;
  }
} 