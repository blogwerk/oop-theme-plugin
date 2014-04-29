<?php
/**
 * Class AbstractWidget
 *
 * Base class for widget class
 *
 * @category    Blogwerk
 * @package     Blogwerk_Theme
 * @subpackage  Widgets
 * @author      Tom Forrer <tom.forrer@blogwerk.com>
 * @copyright   Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
namespace Blogwerk\Widget;

use \Blogwerk\Widget\FormField;
use \WP_Widget;

/**
 * Class AbstractWidget
 *
 * Base class for widget class
 *
 * @category    Blogwerk
 * @package     Blogwerk_Widget
 * @author      Tom Forrer <tom.forrer@blogwerk.com>
 * @copyright   Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
abstract class AbstractWidget extends WP_Widget
{

  protected $version = 1.0;

  protected $baseId = 'AbstractWidget';

  protected $widgetName = 'Abstract Widget';

  protected $textDomain = 'default';

  protected $options = array();

  protected $title;

  protected $useCache = false;
  /**
   * @var string the cache key base to use for widget caching
   */
  protected $cacheKey = '';

  protected $cacheTime = 300;

  protected $fields = array();

  protected $description = '';

  public function __construct()
  {
    if (!isset($this->options['description'])) {
      $this->options['description'] = $this->getDescription();
    } elseif (!$this->getDescription()) {
      $this->description = $this->options['description'];
    }
    $this->options['classname'] = $this->getBaseId();
    $this->cacheKey = $this->getBaseId() . '_' . get_locale();

    parent::__construct($this->getBaseId(), $this->getWidgetName(), $this->getOptions());

    // before the WP_Widget_Factory uses the properties (at widgets_init(100))
    add_action('widgets_init', array($this, 'applyProperties'), 90);

    // allow custom initialization
    add_action('widgets_init', array($this, 'init'), 10);
  }

  /**
   * @return void
   */
  abstract public function init();

  public function applyProperties()
  {
    $options = $this->getOptions();
    if (!isset($options['classname'])) {
      $options['classname'] = $this->getBaseId();
      $this->options = $options;
    }
    $this->widget_options = $this->getOptions();
    $this->name = $this->getWidgetName();
  }

  /**
   * @param array $args
   * @param array $instance
   */
  public function widget($args, $instance)
  {
    $cacheKey = $this->getCacheKey() . '_' . md5(json_encode($instance));
    $widgetHtml = false;
    if ($this->getUseCache()) {
      $widgetHtml = wp_cache_get($cacheKey, $this->getTextDomain());
    }
    if ($widgetHtml === false) {

      $widgetHtml = $this->beforeWidget($args, $instance);

      $html = $this->html($args, $instance);
      if ($html) {
        $widgetHtml .= $html;
        $widgetHtml .= $this->afterWidget($args, $instance);
        if ($this->getUseCache()) {
          wp_cache_set($cacheKey, $widgetHtml, $this->getTextDomain(), $this->getCacheTime());
        }
      } else {
        $widgetHtml = '';
      }
    }
    if ($widgetHtml) {
      echo $widgetHtml;
    }
  }

  /**
   * @param array $args
   * @param array $instance
   * @return mixed
   */
  abstract protected function html($args, $instance);

  /**
   * @param FormField\AbstractFormField $formField
   */
  protected function registerFormField($formField)
  {
    $this->fields = array_merge($this->fields, array($formField->getSlug() => $formField));
  }

  /**
   * @param $field
   * @param array $options
   */
  protected function registerAttachmentFormField($field, $options=array()){
    $formField = new FormField\WidgetAttachmentFormField($field, $this, $options);
    $this->registerFormField($formField);
  }

  /**
   * @param $field
   * @param array $options
   */
  protected function registerTextFormField($field, $options=array()){
    $formField = new FormField\WidgetTextFormField($field, $this,  $options);
    $this->registerFormField($formField);
  }
  /**
   * @param $field
   * @param array $options
   */
  protected function registerEditorFormField($field, $options=array()){
    $formField = new FormField\WidgetEditorFormField($field, $this,  $options);
    $this->registerFormField($formField);
  }

  /**
   * @param array $instance
   * @return string|void
   */
  public function form($instance)
  {
    $html = '';
    foreach ($this->fields as $field => $formField) {
      /**
       * @var FormField\AbstractFormField $formField;
       */
      $fieldHtml = $formField->display($instance[$field], $instance);
      if ($fieldHtml) {
        $html .= $fieldHtml;
      }
    }
    echo $html;
  }

  /**
   * @param array $newInstance
   * @param array $oldInstance
   * @return array
   */
  public function update($newInstance, $oldInstance)
  {
    foreach ($this->fields as $field => $formField) {
      /**
       * @var FormField\AbstractFormField $formField;
       */
      if (isset($newInstance[$field])) {
        $newInstance[$field] = $formField->sanitize($newInstance[$field], $oldInstance[$field]);
      }elseif(isset($newInstance['nonce']) && isset($newInstance[$field . '_' . $newInstance['nonce']])){
        $newInstance[$field] = $formField->sanitize($newInstance[$field . '_' . $newInstance['nonce']], $oldInstance[$field]);
      }else{
        $newInstance[$field] = $formField->getDefaultValue();
      }
    }
    return $newInstance;
  }

  /**
   * Helper function to be optionally overridden
   *
   * @param array $args
   * @param array $instance
   * @return string
   */
  protected function beforeWidget($args, $instance){
    return $args['before_widget'];
  }

  /**
   * Helper function to be optionally overridden
   *
   * @param array $args
   * @param array $instance
   * @return string
   */
  protected function afterWidget($args, $instance){
    return $args['after_widget'];
  }

  /**
   * @return string
   */
  public function getBaseId()
  {
    return $this->baseId;
  }

  /**
   * @return float
   */
  public function getVersion()
  {
    return $this->version;
  }

  /**
   * @return string
   */
  public function getWidgetName()
  {
    return $this->widgetName;
  }

  /**
   * @deprecated use getTextDomain()
   * @return string
   */
  public function getDomain()
  {
    return $this->textDomain;
  }

  /**
   * @return string
   */
  public function getTextDomain()
  {
    return $this->textDomain;
  }

  /**
   * @return array
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * @return boolean
   */
  public function getUseCache()
  {
    return $this->useCache;
  }

  /**
   * @return int
   */
  public function getCacheTime()
  {
    return $this->cacheTime;
  }

  /**
   * @return int
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @return string
   */
  public function getCacheKey()
  {
    return $this->cacheKey;
  }

}