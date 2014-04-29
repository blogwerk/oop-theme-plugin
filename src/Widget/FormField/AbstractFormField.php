<?php
/**
 * Class AbstractFormField
 *
 * @category Blogwerk
 * @package Blogwerk_Widget
 * @subpackage FormField
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
namespace Blogwerk\Widget\FormField;

use \Blogwerk\Widget\AbstractWidget;

/**
 * Class AbstractFormField
 *
 * @category Blogwerk
 * @package Blogwerk_Widget
 * @subpackage FormField
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
abstract class AbstractFormField
{

  /**
   * @var string $slug the field name
   */
  protected $slug;

  /**
   * @var mixed $defaultValue the default value (override this, or set in the options construction argument with the key 'default')
   */
  protected $defaultValue;

  /**
   * @var string $label the form field label
   */
  protected $label = '';

  /**
   * @var array $options can contain the 'default' value or the 'label' value
   */
  protected $options = array();

  /**
   * @var AbstractWidget $widget reference to the widget containing this form field
   */
  protected $widget;

  /**
   * Form field construction recevies a name (field slug), a reference to the parent widget and optionally an options array
   *
   * @param string $fieldSlug
   * @param AbstractWidget $widget
   * @param array $options
   */
  public function __construct($fieldSlug, $widget, $options = array())
  {
    $this->slug = $fieldSlug;
    $this->options = $options;
    $this->widget = $widget;
    if (isset($options['default'])) {
      $this->defaultValue = $options['default'];
    }
    if (isset($options['label'])) {
      $this->label = $options['label'];
    }

    if (is_admin()) {
      add_action('sidebar_admin_setup', array($this, 'setup'));
    }
  }

  /**
   * @param $value
   * @param array $instance
   * @return mixed
   */
  abstract public function display($value, $instance);

  /**
   * Sanitize callback
   *
   * @param mixed $newValue
   * @param mixed $oldValue
   * @return mixed
   */
  public function sanitize($newValue, $oldValue)
  {
    if (isset($this->options['sanitize']) && is_callable($this->options['sanitize'])) {
      $widgetOptions = $this->getWidget()->getOptions();
      $sanitizeArguments = array($newValue, $oldValue, $this->getSlug()) + $widgetOptions;
      return callUserFunctionWithSafeArguments($this->options['sanitize'], $sanitizeArguments);
    } else {
      return $newValue;
    }
  }

  /**
   * called at sidebar_admin_setup(10): can be overridden to setup the field during the sidebar setup
   */
  public function setup()
  {

  }

  /**
   * @return mixed
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }

  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }

  /**
   * @return string
   */
  public function getSlug()
  {
    return $this->slug;
  }

  /**
   * @return array
   */
  public function getOptions()
  {
    return $this->options;
  }

  public function getWidgetOption($key){
    $widgetOptions = $this->getWidget()->getOptions();
    if(isset($widgetOptions[$key])){
      return $widgetOptions[$key];
    }else{
      return null;
    }
  }

  public function getOption($key){
    $fieldOptions = $this->getOptions();
    if(isset($fieldOptions[$key])){
      return $fieldOptions[$key];
    }else{
      return null;
    }
  }

  /**
   * @return \Blogwerk\Widget\AbstractWidget
   */
  public function getWidget()
  {
    return $this->widget;
  }

}

