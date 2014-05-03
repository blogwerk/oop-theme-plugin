<?php
/**
 * @category Blogwerk
 * @package Blogwerk_Theme
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */

namespace Blogwerk\Theme;

use Pimple;
use \Twig_Environment;
use \Twig_Loader_Filesystem;
use \Twig_Loader_Chain;
use \Twig_SimpleFilter;
use \Blogwerk\Theme\WordPressGlobalsWrapper;
use \Blogwerk\Twig\FunctionProxy;

/**
 * Class ServiceContainer
 *
 * A pimple service container
 *
 * @see https://github.com/fabpot/Pimple#packaging-a-container-for-reusability
 * @category Blogwerk
 * @package Blogwerk_Theme
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
class ServiceContainer extends Pimple
{
  const WORDPRESS_GLOBALS = 'wordpress_globals';
  const TWIG_LOADER = 'twig_loader';
  const TWIG = 'twig';
  const PATH = 'theme_path';
  const THEME = 'theme';
  const TWIG_PROXIES = 'twig_proxies';

  /**
   * Service container constructor: define the needed services as a package.
   */
  public function __construct()
  {
    $this[static::PATH] = '.';

    $this['map_folder'] = function ($c) {
      return function ($folder) use ($c) {
        if (is_array($c[static::PATH])) {
          return array_map(function ($path) use ($folder) {
            return $path . $folder;
          }, $c[static::PATH]);
        } else {
          return $c[static::PATH] . $folder;
        }
      };
    };
    $this['view_path'] = function ($c) {
      return $c['map_folder']('views');
    };
    $this['cache_path'] = function ($c) {
      return $c['map_folder']('cache');
    };
    $this['asset_path'] = function ($c) {
      return $c['map_folder']('resources');
    };

    $this[static::WORDPRESS_GLOBALS] = function ($c) {
      return new WordPressGlobalsWrapper();
    };

    // provide a twig loader
    $this[static::TWIG_LOADER] = function ($c) {
      $loaders = array();
      $viewPaths = $c['view_path'];
      if (!is_array($viewPaths)) {
        $viewPaths = array($viewPaths);
      }
      foreach ($viewPaths as $viewPath) {
        if (is_dir($viewPath)) {
          $loaders[] = new Twig_Loader_Filesystem($viewPath);
        }
      }

      return new Twig_Loader_Chain($loaders);
    };

    // provide a twig wordpress proxy object
    $this[static::TWIG_PROXIES] = function ($c) {
      return array('fn' => new FunctionProxy());
    };

    // provide a twig environment with a cache folder and the function proxy
    $this[static::TWIG] = function ($c) {
      $twig = new Twig_Environment($c[static::TWIG_LOADER], array(
        //'cache' => $c['cache_path'],
        'debug' => true
      ));

      foreach($c[static::TWIG_PROXIES] as $key => $proxy){
        $twig->addGlobal($key, $proxy);
      }


      return $twig;
    };
  }

} 