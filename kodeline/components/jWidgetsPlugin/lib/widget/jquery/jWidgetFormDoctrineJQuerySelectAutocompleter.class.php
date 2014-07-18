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
class jWidgetFormDoctrineJQuerySelectAutocompleter extends sfWidgetFormDoctrineChoice
{
  /**
   * Widget options.
   */
  protected $default_widget_options = array(
    'begin_choices'     => array(),
    'finish_choices'    => array(),
    'delay'             => 250, //delay between ajax request (bigger delay, lower server time request)
    'input_min_size'    => 1, //minimum size of the input element (default: 1)
    'json_url'          => null,  //url to fetch json object
    'json_type'         => 'GET',  //url to fetch json object
    'name'              => null, //name of the action.

    'autocomplete_css_class' => null, //name of the action.
    'tooltip_config' => '{ "tooltipClass": "ui-state-highlight" }', //name of the action.

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
    
    'addontab'          => null, //add first visible element on tab or enter hit
    'attachto'          => null, //after this element fcbkcomplete insert own elements
    'bricket'           => true, //use square bricket with select (needed for asp or php) enabled by default
    'input_tabindex'    => null, //the tabindex of the input element
    'input_name'        => null, //value of the input element's 'name'-attribute (no 'name'-attribute set if empty)
    
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
/*
var displayLimit = 7;
$("#search").autocomplete({
    source: function(request, response) {
        $.ajax({
            url: "http://api.stackoverflow.com/1.1/tags",
            data: {
                filter: request.term
            },
            type: "GET",
            success: function(data) {
                //Custom Text
                var item = [{
                    name: "Custom text at Top",
                    count: 0,
                    fulfills_required: false},
                {
                    name: "Custom text at bottom",
                    count: 7,
                    fulfills_required: false}];
                //Adding custom item at the top of list. 
                data.tags.splice(0, 0, item[0]);
                //Adding custom item at the end of list.
                data.tags.splice(6, 0, item[1]);
                response($.map(data.tags, function(el, index) {
                    if (index < displayLimit) {
                        return {
                            value: el.name
                        }
                    }
                }));
            },
            jsonp: "jsonp",
            dataType: "jsonp"
        });
    }
});
*/
    // Set value of the option "template".
    $this->addOption('template', <<<EOF
%associated%
<script type="text/javascript">
/* <[CDATA[ */
jQuery(document).ready(function() {
  jQuery("#%id%").autocomplete2({ ajaxUrl: '%url%', topMenu: '#submenu' });
});
/* ]]> */
</script>
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

    // Define widget for autoselect.
    $associatedWidget = new sfWidgetFormSelect(
                          array('multiple'  => false, 'choices'   => $choices, 'default'   => $choices),
                          array('class' => 'custom.combobox')
                        );

    return strtr($this->getOption('template'), array(
      '%id%'                => $this->generateId($name),
      '%url%'               => $this->getUrl(sfContext::getInstance()->getRouting()->generate('j_widgets_suggest_search', array('action' => 'list'))  . '?name=%name%'),
      '%json_type%'         => $this->getOption('json_type'),
      '%min_length%'        => $this->getOption('input_min_size'),
      '%autocomplete_css_class%'        => $this->getOption('autocomplete_css_class'),
      '%tooltip_config%'    => $this->getOption('tooltip_config'),
      '%delay%'             => $this->getOption('delay'),
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
    if (preg_match('/%[\w]+%/si', $template, $matches))
    {
      $szMatches  = count($matches);
      for($i = 0; $i < $szMatches; $i++)
      {
        $truncateName = str_replace('%', null, $matches[$i]);
        if ($this->hasOption($truncateName))
        {
          $template = str_replace($matches[$i], $this->getOption($truncateName), $template);
        }
      }
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
    return array_merge(parent::getJavascripts(), array('//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js'));
  }
}
