<?php
/**
 * Class WidgetTextareaFormField
 * @category Blogwerk
 * @package Blogwerk_Widget
 * @subpackage FormField
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */

namespace Blogwerk\Widget\FormField;

/**
 * Class WidgetTextareaFormField
 *
 * @category Blogwerk
 * @package Blogwerk_Widget
 * @subpackage FormField
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
class WidgetTextareaFormField
{
  /**
   *
   * @param mixed $value
   * @param array $instance
   * @return string
   */
  public function display($value, $instance)
  {
    if (!isset($value)) {
      $value = $this->getDefaultValue();
    }

    $html = '
    <p>
        <label for="' . $this->getWidget()->get_field_id($this->getSlug()) . '">' . $this->getLabel() . '</label>
        <textarea class="widefat" name="' . $this->getWidget()->get_field_name($this->getSlug()) . '" id="' . $this->getWidget()->get_field_id($this->getSlug()) . '">' . $value . '</textarea>
    </p>
    ';
    return $html;
  }
} 