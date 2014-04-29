<?php
/**
 * Class WidgetAttachmentFormField
 *
 * @category Blogwerk
 * @package Blogwerk_Widget
 * @subpackage FormField
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */

namespace Blogwerk\Widget\FormField;

use \SmkMetaboxHelper;

/**
 * Class WidgetAttachmentFormField
 *
 * @category Blogwerk
 * @package Blogwerk_Widget
 * @subpackage FormField
 * @author Tom Forrer <tom.forrer@blogwerk.com
 * @copyright Copyright (c) 2014 Blogwerk AG (http://blogwerk.com)
 */
class WidgetAttachmentFormField extends AbstractFormField
{
  public function setup()
  {
    $this->enqueueMediaUploaderAssets();
  }

  /**
   * @param $value
   * @param array $instance
   * @return mixed|string
   */
  public function display($value, $instance)
  {

    $styleAttribute = '';
    $imageHtml = '';
    $valueAttribute = '';
    $attachment = get_post(intval($value));
    $mediaUploaderClass = '';
    if ($attachment) {
      list($url, $width, $height, $crop) = wp_get_attachment_image_src($attachment->ID, 'medium');
      $styleAttribute = 'style="padding: ' . number_format(100 * ($height / $width), 2, '.', '') . '% 0 0 0;"';
      $imageHtml = '<img src="' . $url . '" />';
      $valueAttribute = 'value="' . $attachment->ID . '"';
      $mediaUploaderClass = 'has-attachment';
    }
    $html = '
      <div class="media-uploader attachment-field-' . $this->getWidget()->get_field_id($this->getSlug()) . ' ' . $mediaUploaderClass . '">
        <div class="image-wrapper wrapper" ' . $styleAttribute . '>' . $imageHtml . '</div>
        <input type="button" class="button" id="' . $this->getWidget()->get_field_id('upload') . '" name="' . $this->getWidget()->get_field_name('upload') . '" value="' . $this->getLabel() . '"  />
        <input type="hidden" class="field-attachment-id" id="' . $this->getWidget()->get_field_id($this->getSlug()) . '" name="' . $this->getWidget()->get_field_name($this->getSlug()) . '" ' . $valueAttribute . ' />
      </div>
      <script>
      (function ($) {
        $(document).ready(function(){
          $(".widget .media-uploader.attachment-field-' . $this->getWidget()->get_field_id($this->getSlug()) . '").wordpressAttachment();
        });
      }(jQuery));
    </script>
      ';

    return $html;
  }

  /**
   *
   */
  public function enqueueMediaUploaderAssets()
  {
    // necessary for wp.media to work (already handled in post edit screen, but not for widgets.php)
    wp_enqueue_media();
    wp_enqueue_script(
      'media-uploader-js',
      '/wp-content/plugins/oop-theme-plugin/resources/scripts/media-uploader.js',
      array('jquery', 'media-upload', 'media-views'),
      $this->getWidget()->getVersion()
    );
    wp_enqueue_style(
      'media-uploader-css',
      '/wp-content/plugins/oop-theme-plugin/resources/styles/media-uploader.css',
      array(),
      $this->getWidget()->getVersion()
    );
  }
} 