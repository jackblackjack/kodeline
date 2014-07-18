<?php
/**
 * sfWidgetFormDoctrineFBAutocompleter represents an autocompleter input widget rendered by JQuery.
 *
 * This widget needs JQuery to work.
 *
 * You also need to include the JavaScripts and stylesheets files returned by the getJavaScripts()
 * and getStylesheets() methods.
 *
 * If you use symfony 1.2, it can be done automatically for you.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Gregory SCHURGAST <fgreg@negko.com>
 */
class jWidgetFormDoctrineFBSuggest extends sfWidgetFormDoctrineChoice
{
  /**
   * Widget options.
   */
  protected $default_widget_options = array(
    'name'              => null, //name of the action.
    'width'             => null, //element width
    'cache'             => null, //use cache
    'height'            => null, //maximum number of element shown before scroll will apear
    'newel'             => null, //show typed text like a element
    'firstselected'     => null, //automaticly select first element from dropdown
    'filter_case'       => null, //case sensitive filter
    'filter_selected'   => null, //filter selected items from list
    'filter_begin'      => null, //filter only from begin
    'complete_text'     => null, //text for complete page
    'maxshownitems'     => null, //maximum numbers that will be shown at dropdown list (less better performance)
    'oncreate'          => null, //fire event on item create
    'onselect'          => null, //fire event on item select
    'onremove'          => null, //fire event on item remove
    'maxitems'          => null, //maximum items that can be added
    'delay'             => null, //delay between ajax request (bigger delay, lower server time request)
    'addontab'          => null, //add first visible element on tab or enter hit
    'attachto'          => null, //after this element fcbkcomplete insert own elements
    'bricket'           => true, //use square bricket with select (needed for asp or php) enabled by default
    'input_tabindex'    => null, //the tabindex of the input element
    'input_min_size'    => null, //minimum size of the input element (default: 1)
    'input_name'        => null, //value of the input element's 'name'-attribute (no 'name'-attribute set if empty)
    'json_url'          => null, //url to fetch json object,
    'async'             => null
  );

  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * url:            The URL to call to get the choices to use (required)
   *  * config:         A JavaScript array that configures the JQuery autocompleter widget
   *  * value_callback: A callback that converts the value before it is displayed
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    // Set values of the default options.
    $arKeys = array_keys($this->default_widget_options);
    $szKeys = count($arKeys);
    for($i = 0; $i < $szKeys; $i++)
    {
      $this->addOption($arKeys[$i], $this->default_widget_options[$arKeys[$i]]); 
    }

    // Set value of the option name "json_url".
    //$this->addOption('json_url', sfContext::getInstance()->getRouting()->generate('j_fb_suggest_search', array('action' => 'list')) . '?name=%name%&model=%model%');
    $this->addOption('json_url', sfContext::getInstance()->getRouting()->generate('j_fb_suggest_search', array('action' => 'list')) . '?name=%name%');

    // Set value of the option "template".
    $this->addOption('template', <<<EOF
%associated%
<script type="text/javascript">/*<[CDATA[*/
jQuery(document).ready(function() { jQuery("#%id% option").attr('selected','selected'); jQuery("#%id%").fcbkcomplete(%config%); });
/*]]>*/</script>
EOF
    );

    parent::configure($options, $attributes);
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $options = array_intersect_key($this->getOptions(), $this->default_widget_options);

    $options['json_url'] = $this->getUrl($this->getOption('json_url'));

    $value = (array)$value;

    $choices = array();

    if (count($value))
    {
      $records = Doctrine_Core::getTable($this->getOption('model'))->createQuery()->whereIn('id', $value)->execute();
      $method = $this->getOption('method');

      $key_method = $this->getOption('key_method');
      foreach ($records as $record)
      {
        $choices[$record->$key_method()] = $record->$method();
      }
    }

    $options = array_filter($options);
    //foreach($options as $k => $v) if (is_null($v)) unset($options[$k]);

    $config = json_encode($options, JSON_FORCE_OBJECT);

    $associatedWidget = new sfWidgetFormSelect(array('multiple' => true, 'choices' => $choices, 'default' => $choices));

    return strtr($this->getOption('template'), array(
      '%id%'                => $this->generateId($name),
      '%config%'            => $config,
      '%associated%'        => $associatedWidget->render($name)
    ));
  }

  /**
   * Format url template by replace options values of the keys in url.
   * 
   * @param string $template Template of the url string.
   * @return string
   */
  public function getUrl($template)
  {
    $matches = array();
    if (preg_match_all('/(?<name>(\w+))?=?(?<param>(%[\w]+%))/si', $template, $matches))
    {
      $szMatches  = count($matches['name']);
      for($i = 0; $i < $szMatches; $i++)
      {
        //$truncateName = str_replace('%', null, $matches[$i]);
        if ($this->hasOption($matches['name'][$i]))
        {
          $template = str_ireplace($matches['param'][$i], $this->getOption($matches['name'][$i]), $template);
        }
      }

      // Clean string.
      $template = preg_replace('/(\w+)=(%[\w]+%)/si', null, $template);
    }

    return $template;
  }

  /**
   * Gets the stylesheet paths associated with the widget.
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return array('/jDoctrineFBSuggestPlugin/css/style.css' => 'all');
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return array('/jDoctrineFBSuggestPlugin/js/jquery.fcbkcomplete.min.js');
  }
}
