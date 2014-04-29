<?php
/**
 * Class WidgetEditorFormField
 *
 * @category Blogwerk
 * @package Blogwerk_Widget
 * @subpackage FormField
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */

namespace Blogwerk\Widget\FormField;

use \Blogwerk\Util\String;

/**
 * Class WidgetEditorFormField
 *
 * @category Blogwerk
 * @package Blogwerk_Widget
 * @subpackage FormField
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
class WidgetEditorFormField extends AbstractFormField
{

  public function setup(){
    if(is_admin()){
      wp_enqueue_script('tiny-mce-fixes-js', plugins_url('js/tinyMceWidgetFixes.js', __DIR__), array('jquery'));
    }
  }
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
    $widget = $this->getWidget();
    $nonce = hash('md5', microtime());

    $editorName = $widget->get_field_name($this->getSlug() . '_' . $nonce);
    $editorId = $widget->get_field_id($this->getSlug() . '_' . $nonce);
    $nonceName = $widget->get_field_name('nonce');
    $nonceId = $widget->get_field_id('nonce');

    // http://wordpress.stackexchange.com/questions/82670/why-cant-wp-editor-be-used-in-a-custom-widget
    $html = String::getWpEditor($value, $editorId, array(
      'media_buttons' => false,
      'textarea_rows' => 3,
      'textarea_name' => $editorName,
      'teeny' => true,
    ));
    $html .= '<input type="hidden" id="' . $nonceId . '" name="' . $nonceName . '" value="' . $nonce . '" />';

    $html .= '
    <script type="text/javascript">
      jQuery(document).ready(function ($) {
        var editorId = "' . $editorId .'";
        var isAjaxMode = ' . json_encode( defined( 'DOING_AJAX' ) && DOING_AJAX == true ) . ';

        // register a callback on each widget save button: tinyMCE needs to save the contents to the textarea and deregister the editor, as it gets replaced
        $("#" + editorId).closest(".widget").find("input.widget-control-save").on("click", function (e) {
          if (editorExists(editorId)) {
            // save the content to the textarea
            tinyMCE.triggerSave();
            // this is an tinyMCE IE bug: we need to focus, before removing
            tinyMCE.execCommand("mceFocus", false, editorId);
            tinyMCE.execCommand("mceRemoveControl", false, editorId);
          }
        });

        // on page load, wordpress has already told tinyMCE to initialize the editor,
        // but after saving and reloading the widget form with ajax, we need to reinitialize the editor (if possible)
        if (isAjaxMode && $("#" + editorId).length > 0 && !editorExists(editorId)) {
          $(document).ajaxComplete(function (e, xhr, settings) {
            // this function is in blogwerk_promotion/js/tinyMceWidgetFixes.js
            fixTinyMceWidget(editorId);
          });
        }
      });
    </script>
    ';
    return $html;
  }
} 