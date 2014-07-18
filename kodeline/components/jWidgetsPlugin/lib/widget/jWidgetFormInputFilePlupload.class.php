<?php
/**
 * Widget for set value of date to field.
 * Use JQuery ui datepicker.
 * 
 * @package     jWidgetsPlugin
 * @subpackage  plupload-widget
 * @category    file
 * @link        http://www.plupload.com/
 * 
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class jWidgetFormInputFilePlupload extends sfWidgetFormInputFileEditable
{
  /**
   * Url for upload file.
   */
  protected $sUploadUrl = null;

  /**
   * Key for form file.
   * @var string
   */
  protected $sFormKey = null;

  /**
   * Constructor.
   *
   * Available options:
   *  * plupload_options          : Associative array of Tiny MCE options (empty array by default)
   *  * plupload_path             : Path to TinyMCE
   *  * options_without_quotes: Options without quotes (only "setup" by default)
   *
   * @see sfWidgetFormTextarea
   **/    
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    // Set form name option.
    $this->addRequiredOption('form');

    // Set drag and drop feature to disable as default.
    $this->addOption('use_dragndrop', false);

    // Set form key option.
    $this->addOption('key', null);

    // Set option for image's loader.
    $this->addOption('path_to_loader_image', sfConfig::get('app_plupload_loader_image', '/images/preloader/1601.gif'));

    // Set options for plupload.
    $this->addOption('plupload_options', sfConfig::get('app_plupload_default', array(
      'runtimes'        => 'gears,browserplus,html5,html4',
      'multiple_queues' => false,
      'max_file_size'   => '100mb'
    )));

    $this->addOption('plupload_path', sfConfig::get('app_plupload_path', '/jWidgetsPlugin/js/'));
    //$this->addOption('template', '%file%<br />%input%<br />%delete% %delete_label% %script%');

    $this->addOption('template', '%html%%hidden%%script%');
    $this->addOption('template_list', '');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @see sfWidget
   **/    
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    // Fetch field html code.
    $input = sfWidgetFormInputFile::render($name, $value, $attributes, $errors);

    // Fetch field html code.
    $hiddenKeyField = new sfWidgetFormInputHidden();
    $hiddenKey = $hiddenKeyField->render($name . '[key]', $this->getOption('key'));

    /*
    if (!$this->getOption('edit_mode'))
    {
      return $input;
    }

    if ($this->getOption('with_delete'))
    {
      $deleteName = ']' == substr($name, -1) ? substr($name, 0, -1).'_delete]' : $name.'_delete';

      $delete = $this->renderTag('input', array_merge(array('type' => 'checkbox', 'name' => $deleteName), $attributes));
      $deleteLabel = $this->translate($this->getOption('delete_label'));
      $deleteLabel = $this->renderContentTag('label', $deleteLabel, array_merge(array('for' => $this->generateId($deleteName))));
    }
    else
    {
      $delete = '';
      $deleteLabel = '';
    }

    return strtr($this->getOption('template'), array('%input%' => $input, '%delete%' => $delete, '%delete_label%' => $deleteLabel, '%file%' => $this->getFileAsTag($attributes)));
    */

    // Fetch current options.
    $arOptions = $this->getOption('plupload_options');
      
    // Generate content id.
    $id = $this->generateId($name, $value);

    // Set plupload container name.
    if (! array_key_exists('container', $arOptions)) {
      $arOptions['container'] = sprintf('%s-container', $id);
    }

    // Set plupload browse button name.
    if (! array_key_exists('browse_button', $arOptions)) {
      $arOptions['browse_button'] = sprintf('%s-browse', $id);
    }

    // Set plupload browse button name.
    if (! array_key_exists('drop_element', $arOptions) && $this->getOption('use_dragndrop')) {
      $arOptions['drop_element'] = sprintf('%s-dropzone', $id);
    }

    // Generate url.
    $url = url_for2('j_file_attach_post_ajax', array(
      'form'      => $this->getOption('form'),
      'field'     => 'value',
      'sf_format' => 'json',
      'key'       => ($this->hasOption('key') ? $this->getOption('key') : null)
    ));

    // Set url for plupload configuration.
    if (! array_key_exists('url', $arOptions)) {
      $arOptions['url'] = $url;
    }

    // Merge options.
    $this->setOption('plupload_options', $arOptions);

    // Set plupload loader image.
    $image_url = $this->getOption('path_to_loader_image');

    // Fetch options as json.    
    $options = json_encode($this->getOption('plupload_options'));

    if ($this->getOption('use_dragndrop'))
    {
      // Generate HTML content.
      $html_content = <<<HTML
        <div id="{$id}-dropzone">
          <span>Максимальный размер 100MB</span>
        </div>
HTML;
    }
    else {

      // Generate HTML content.
      $html_content = <<<HTML
        <div>
          <div>
            <span id="{$id}-container">
              <a id="{$id}-href" href="{$url}"></a>
              <span id="{$id}-browse">Выберите файл</span>
            </span>
            <span id="{$id}-status">Файл не выбран.</span>
          </div>
          <span>Максимальный размер 100MB</span>
        </div>
HTML;
  }

    $script_content = <<<JS
/* <![CDATA[ */
function upl{$id}Init() {
  try {
    if ('undefined' == typeof(jQuery)) { throw new Error("Библиотека jQuery не загружена"); }

    var upl{$id} = new plupload.Uploader({$options});   

    upl{$id}.bind('BeforeUpload', function (up, file) {
      up.settings.multipart_params = {'originalFileName':file.name,'totalSize':file.size,'relativePath':file.relativePath}
    });

    upl{$id}.bind('QueueChanged', function(up) {
      jQuery('#{$id}-status').html('<img src="{$image_url}" /> загрузка файла..');
      up.start();
      up.refresh();
    });

    upl{$id}.bind('Error', function(up, err) {
      jQuery('#{$id}-status').html('Ошибка загрузки файла.');
      up.refresh();
    });

    upl{$id}.bind('FileUploaded', function(up, file, response) {
      var data = jQuery.parseJSON(response.response);
      if (null == data) return false;

      if ('undefined' !== typeof(data.error))
      {
        jQuery('#{$id}-status').html('Ошибка загрузки файла.');
        return false;
      }

      if ('undefined' !== typeof(data.result)) {     
        jQuery('#{$id}-status').html('Файл загружен.');
      }
    });

    upl{$id}.init();
  }
  catch(e) {
    jQuery('#{$id}-status').html(e.message);
  }
}
(function(){ upl{$id}Init(); })();
/* ]]> */
JS;

    return strtr($this->getOption('template'), 
              array(
                '%html%'   => $html_content,
                '%hidden%' => $hiddenKey,
                //'%input%' => $input, 
                /*'%delete%' => $delete, 
                '%delete_label%' => $deleteLabel, */
                //'%file%' => $this->getFileAsTag($attributes),
                '%script%' => $this->renderContentTag('script', $script_content, array('type' => 'text/javascript')),
              )
            );
  }

  /**
   * @see sfWidgetFormInputFileEditable
   */
  protected function getFileAsTag($attributes)
  {
    if ($this->getOption('is_image'))
    {
      return false !== $this->getOption('file_src') ? $this->renderTag('img', array_merge(array('src' => $this->getOption('file_src')), $attributes)) : '';
    }
    else
    {
      return $this->getOption('file_src');
    }
  }

  /**
   * @see sfWidget
   */
  public function getJavascripts()
  {
    return array_unique(array_merge(
        parent::getJavaScripts(),
        array($this->getOption('plupload_path') . 'plupload/plupload.full.min.js')
      )
    );
  }
}
