<?php
/**
 * jWidgetFormDoctrineJQuerySelect2 represents an select box input widget rendered by JQuery.
 * 
 *
 * @package   jWidgetsPlugin
 * @category  widget
 * @link      http://ivaynberg.github.io/select2/
 * @author    chugarev@gmail.com
 */
class jWidgetFormJQuerySelect2 extends sfWidgetFormChoiceBase
{
  /**
   * Widget options.
   */
  protected $default_widget_options = array(
    'width'                   => '100%',
    'minimumInputLength'      => 2,
    'maximumInputLength'      => 10,
    'minimumResultsForSearch' => '',
    'maximumSelectionSize'    => '',
    'placeholder'             => '',
    'placeholderOption'       => '',
    'separator'               => '',
    'allowClear'              => false,
    'closeOnSelect'           => '',
    'openOnEnter'             => '',
    'matcher'                 => '',
    'sortResults'             => '',
    'formatSelection'         => '',
    'formatResult'            => '',
    'formatResultCssClass'    => '',
    'formatNoMatches'         => '',
    'formatSearching'         => '',
    'formatInputTooShort'     => '',
    'formatSelectionTooBig'   => '',
    'createSearchChoice'      => '',
    'initSelection'           => '',
    'tokenizer'               => '',
    'tokenSeparators'         => '',
    'query'                   => '',
    'ajax'                    => '',
    'data'                    => '',
    'tags'                    => '',
    'containerCss'            => '',
    'containerCssClass'       => '',
    'dropdownCss'             => '',
    'dropdownCssClass'        => '',
    'dropdownAutoWidth'       => '',
    'adaptContainerCssClass'  => '',
    'adaptDropdownCssClass'   => '',
    'escapeMarkup'            => '',
    'selectOnBlur'            => '',
    'loadMorePadding'         => '',
    'callbacks'               => array(),
    'choices_query'           => null,
    'choices'                 => array()
  );

  protected $internal_widget_options = array(
    'select_default_choices'  => array('Не выбрано'),
    'select_attributes'       => array(),
    'add_new_item_allow'      => false,
    'add_new_item_url'        => null,
    'model'                   => null,
    'add_empty'               => false,
    'multiple'                => false,
    'component'               => null,
    'key_record_col'          => 'id',
    'title_record_col'        => 'title',
    'events'                  => array(),
    'as_widget_class'         => 'sfWidgetFormInput'
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
    parent::configure($options, $attributes);

    // Set options list for widget.
    $this->setOptions(array_merge(($this->getOptions() + $this->default_widget_options + $this->internal_widget_options), $options));

    // Set value of the option "template".
    $this->addOption('template', <<<EOF
%associated%
<script type="text/javascript">/* <[CDATA[ */
jQuery(document).ready(function() { 
  jQuery("#%id%").select2(%widget_config%);

  jQuery("#%id% li.select2-no-results").on("click", "#%id%_new_item", function (event) {
    alert("Clicked the button!");
  });
});
/* ]]> */</script>
EOF
    );
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
    $options = $this->getOptions();

    // Define widget class.
    $associatedWidget = $this->configureWidget(array_merge($attributes + $this->getOption('select_attributes')));

    // Define widget id.
    $widgetId = $this->generateId($name);

    return strtr($this->getOption('template'), array(
      '%id%'                      => $widgetId,
      '%url%'                     => $this->getUrl(sfContext::getInstance()->getRouting()->generate('j_widgets_suggest_search', array('action' => 'list'))  . '?name=%name%'),
      '%json_type%'               => $this->getOption('json_type'),
      '%min_length%'              => $this->getOption('input_min_size'),
      '%autocomplete_css_class%'  => $this->getOption('autocomplete_css_class'),
      '%tooltip_config%'          => $this->getOption('tooltip_config'),
      '%delay%'                   => $this->getOption('delay'),
      '%widget_config%'           => $this->generateWidgetConfig($widgetId),
      '%associated%'              => $associatedWidget->render($name, $value)
    ));
  }

  /**
   * Returns the translated choices configured for this widget
   * @return array  An array of strings
   */
  public function getChoices()
  {
    // Define choices list.
    $choices = ($this->getOption('add_empty') ? $this->getOption('select_default_choices') : array()) + $this->getOption('choices');

    if ($this->getOption('choices_query') instanceof Doctrine_Query)
    {
      $records = $this->getOption('choices_query')->fetchArray();
      foreach ($records as $record)
      {
        // Define title column name.
        $title = (array_key_exists($this->getOption('title_record_col'), $record) ?  $record[$this->getOption('title_record_col')] : 
          (array_key_exists('Translation', $record) ? 
          $record['Translation'][sfContext::getInstance()->getUser()->getCulture()][$this->getOption('title_record_col')] : $record['id']));
        
        $choices[$record[$this->getOption('key_record_col')]] = $title;
      }
    }
    elseif (null !== $this->getOption('model'))
    {
      //$query = (null === $this->getOption('query') ? Doctrine_Core::getTable($this->getOption('model'))->createQuery() : $this->getOption('query');
      $query = Doctrine_Core::getTable($this->getOption('model'))->createQuery();

      if ($order = $this->getOption('order_by')) {
        $query->addOrderBy($order[0] . ' ' . $order[1]);
      }

      $records = $query->fetchArray();
      foreach ($records as $record) {
        $choices[$record['id']] = $record['title'];
      }
    }
    /*
    else
    {
      $tableMethod = $this->getOption('table_method');
      $results = Doctrine_Core::getTable($this->getOption('model'))->$tableMethod();

      if ($results instanceof Doctrine_Query)
      {
        $objects = $results->execute();
      }
      else if ($results instanceof Doctrine_Collection)
      {
        $objects = $results;
      }
      else if ($results instanceof Doctrine_Record)
      {
        $objects = new Doctrine_Collection($this->getOption('model'));
        $objects[] = $results;
      }
      else
      {
        $objects = array();
      }
    }
    */

    return $choices;
  }

  /**
   * Configure internal widget for current widget and retrieve it.
   * 
   * @return sfWidget instance
   */
  protected function configureWidget($attributes)
  {
    // Define widget class.
    $widgetClass = $this->getOption('as_widget_class', 'sfWidgetFormInput'); 

    // Create code for html element.
    switch($widgetClass)
    {
      //
      // Widget sfWidgetFormInput
      //
      case 'sfWidgetFormInput':
        $widget = new $widgetClass(
                        array('default' => $this->getDefault()),
                        $attributes
                      );
      break;

      //
      // Widget sfWidgetFormSelect
      //
      case 'sfWidgetFormSelect':
      default:
        $widget = new $widgetClass(array(
                          'multiple'    => $this->getOption('multiple'),
                          'choices'     => $this->getChoices(),
                          'default'     => $this->getDefault()
                        ),
                        $attributes
                      );
      break;
    }

    return $widget;
  }

  /**
   * Generate select2 widget configuration.
   * 
   * @param string $idWidget Id for widget.
   * @return string
   * 
   */
  protected function generateWidgetConfig($idWidget)
  {
    // Define widget class.
    $widgetClass = $this->getOption('as_widget_class', 'sfWidgetFormInput');

    // If allow to add new items.
    if ($this->getOption('add_new_item_allow'))
    {
      //
      // If internal widget is sfWidgetFormInput
      //
      if ('sfWidgetFormInput' === $widgetClass)
      {
        $preDefined = $this->getOption('createSearchChoice');

        if (empty($preDefined))
        {
          $this->setOption('createSearchChoice',
            'function (term, data) {
              if (jQuery(data).filter(function () { return this.text.localeCompare(term) === 0; }).length === 0) {
                return {"id": -1, "text":term};
              }
            }');
        }
        else {
          $this->setOption('createSearchChoice', $preDefined);
        }
      }

      //
      // If internal widget is sfWidgetFormSelect
      //
      elseif ('sfWidgetFormSelect' === $widgetClass)
      {
        $preDefined = $this->getOption('formatNoMatches');

        if (empty($preDefined))
        {
          $this->setOption('formatNoMatches',
            'function (term, data) {
              if (0 < term.length) {
                return \'Совпадений не найдено. <button id="' . $idWidget .'_new_item">Добавить "\' + term + \'"</button>\';
              }
              else {
               return \'Совпадений не найдено. <button id="' . $idWidget .'_new_item">Добавить</button>\';
              }
            }');
        }
        else {
          $this->setOption('formatNoMatches', $preDefined);
        }
      }
    }

    // Configure ajax options.
    $arAjaxConfig = array();
    $arOptionAjax = $this->getOption('ajax');

    if (! empty($arOptionAjax))
    {
      if (is_array($arOptionAjax))
      {
        $arAjaxConfig['url'] = $arOptionAjax['url'];
        $arAjaxConfig['dataType'] = (empty($arOptionAjax['dataType']) ? 'json' : $arOptionAjax['dataType']);
        $arAjaxConfig['data'] = (! empty($arOptionAjax['data']) ? $arOptionAjax['data'] : 'function(term, page) { return { q: term, p: page }; }');
        $arAjaxConfig['results'] = (! empty($arOptionAjax['results']) ? $arOptionAjax['results'] : 'function(response, page) { var result = []; if ("undefined" !== typeof(response.result.values)) { for (var key in response.result.values) { result.push({"id": key, "text": response.result.values[key]}); } } return {"results": result} }');
        $arAjaxConfig['quietMillis'] = 100;

        // Set option ajax.
        $this->setOption('ajax', $arAjaxConfig);
      }
    }

    //$this->setOption('initSelection', 'function(element, callback) { alert("asd"); }');
    //$this->setOption('formatResult', 'function(result) { alert(result); return "aa"; }');

    return $this->optionsToJavascriptObject($this->getOptions());
  }

  /**
   * Return list of widget's options as json.
   * 
   * @param array $options Options for json object.
   * @return string
   */
  public function optionsToJavascriptObject($options)
  {
    // Prepare options for remove nulled options.
    $options = array_filter(array_intersect_key($options, $this->default_widget_options), function($value) { 
      return ((is_string($value) && '' !== trim($value)) || is_bool($value) || (is_array($value) && count($value)));
    });

    // Unset values.
    unset($options['value']);

    // Convert array to compatible javascript json configuration.
    return '{' . implode(',', array_map(function($k, $v) { return sprintf('"%s":%s', $k, jWidgetFormJQuerySelect2::jsonEscape($v)); }, array_keys($options), array_values($options))) . '}';
  }

  /**
   * Escapes a string.
   *
   * @param  string $value  string to escape
   * @return string escaped string
   */
  public static function jsonEscape($value)
  {
    // Recursive call if preparing value its array.
    if (is_array($value)) { 
      return '{' . implode(',', array_map(function($k, $v) { return sprintf('"%s":%s', $k, jWidgetFormJQuerySelect2::jsonEscape($v)); }, array_keys($value), array_values($value))) . '}';
    }

    // Boolean php-values to boolean javascript-values.
    if (is_bool($value)) return ($value ? 'true' : 'false');

    // Clean string if its function.
    if (preg_match('/(?<=)function(?:\s+)?\((?:(?!}").)*}/si', $value)) {
      return sprintf('%s', str_replace(array('\n','\t','\"', '\\\\','\\/'), array('','','"','\\','/'), $value));
    }

    // Return escaped string value.
    return sprintf('"%s"', jWidgetFormJQuerySelect2::fixDoubleEscape(htmlspecialchars((string) $value, ENT_QUOTES, jWidgetFormJQuerySelect2::getCharset())));   
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
    return array('/jWidgetsPlugin/js/select2/select2.css' => 'all');
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return array_merge(parent::getJavascripts(), array(
      '//yandex.st/jquery/2.0.3/jquery.min.js',
      '/jWidgetsPlugin/js/select2/select2.min.js',
      '/jWidgetsPlugin/js/select2/select2_locale_ru.js'
    ));
  }
}
