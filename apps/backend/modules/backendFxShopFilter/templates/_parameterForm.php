<div class="mws-form-row">
  <?php echo $form['rules'][$rule_ix]->renderHiddenFields() ?>

  <div class="mws-form-cols">
    <div class="mws-form-col-2-8">
      <?php echo $form['rules'][$rule_ix]['type_id']->renderLabel(null, array("class" => "mws-form-label")) ?>

      <div class="mws-form-item">
        <?php echo $form['rules'][$rule_ix]['type_id']->render() ?>

        <?php if ($form['rules'][$rule_ix]['type_id']->hasError()): ?>
        <span><?php echo $form['rules'][$rule_ix]['type_id']->renderError() ?></span>
        <?php endif ?>
      </div>
    </div>

    <div class="mws-form-col-1-8">
      <?php echo $form['rules'][$rule_ix]['parameter_id']->renderLabel(null, array("class" => "mws-form-label")) ?>

      <div class="mws-form-item">
        <?php echo $form['rules'][$rule_ix]['parameter_id']->render() ?>
      </div>

      <?php if ($form['rules'][$rule_ix]['parameter_id']->hasError()): ?>
      <span><?php echo $form['rules'][$rule_ix]['parameter_id']->renderError() ?></span>
      <?php endif ?>
    </div>

    <div class="mws-form-col-1-8">
      <?php echo $form['rules'][$rule_ix]['compare']->renderLabel(null, array("class" => "mws-form-label")) ?>

      <div class="mws-form-item">    
        <?php echo $form['rules'][$rule_ix]['compare']->render(array()) ?>

        <?php if ($form['rules'][$rule_ix]['compare']->hasError()): ?>
        <span><?php echo $form['rules'][$rule_ix]['compare']->renderError() ?></span>
        <?php endif ?>
      </div>
    </div>

    <div class="mws-form-col-2-8">
      <?php echo $form['rules'][$rule_ix]['value']->renderLabel(null, array("class" => "mws-form-label")) ?>

      <div class="mws-form-item" id="<?php echo $form['rules'][$rule_ix]['value']->renderId(); ?>_container">    
        <?php echo $form['rules'][$rule_ix]['value']->render(array()) ?>

        <?php if ($form['rules'][$rule_ix]['value']->hasError()): ?>
        <span><?php echo $form['rules'][$rule_ix]['value']->renderError() ?></span>
        <?php endif ?>
      </div>
    </div>
  </div>
</div>

<div class="mws-form-row">
  <div class="btn-group">
    <input type="button" class="btn btn-primary" id="btn-add-rule" value="Новое правило" />
  </div>
</div>


<?php $hlpBroker->js->beginInlineJavascript(); ?>
<!--script-->
var idComponentSelect = '<?php echo $form['rules'][$rule_ix]['type_id']->renderId() ?>';
var idParameterSelect = '<?php echo $form['rules'][$rule_ix]['parameter_id']->renderId() ?>';
var urlGetParams = '<?php echo url_for2('backend_fxshop_json_get_parameters') ?>';
var urlGetValField = '<?php echo url_for2('backend_fxshop_ajax_get_valuefield') ?>';

/* префикс для имени поля */
var paramFieldPrefix = '<?php echo str_replace('[parameter_id]', '[conditions]', $form['rules'][$rule_ix]['parameter_id']->renderName()) ?>';

/**
 * Bind click to add rule button
 */
jQuery('#btn-add-rule').bind("click", function() {
  alert("PRIVET!");
});


/* bind to change component_id */
jQuery('#' + idComponentSelect).bind("change", function() {
  var iComponent = jQuery(this).find('option:selected').val();
  var callUrl = urlGetParams + '?component_id=' + iComponent;

  /* Ajax query */
  jQuery.ajax({
    type: 'POST',
    dataType: "json",
    url: callUrl,
    timeout: 500, 
    beforeSubmit: function(xhr) { 
      jQuery('#' + idParameterSelect).empty();
    },
    success: function(response) {
      jQuery('#' + idParameterSelect).empty();
      if (response.result.length)
      {
        var option = jQuery('<option></option>').attr('value', 0).text('- выберите -');
        jQuery('#' + idParameterSelect).append(option);

        jQuery.each(response.result, function(key, item) {
          var option = jQuery('<option></option>').attr('value', item.id).text(item.title);
          jQuery('#' + idParameterSelect).append(option);
        });
      }
    },
    error: function(error) {},        
    complete: function() {}
  }); 
});

setTimeout(function() { jQuery('#' + idComponentSelect).trigger('change'); }, 1500);

/* bind to change parameter_id */
jQuery('#' + idParameterSelect).bind('change', function() {
  var iComponent = jQuery('#' + idComponentSelect + ' option:selected').val();
  var iParameter = jQuery(this).find('option:selected').val();

  /* количество условий для параметра */
  /*var paramFieldCount = jQuery('#' + '').find('tr.param-container').length;*/
  var paramFieldCount = jQuery('html').find('tr.param-container').length;

  var callUrl = urlGetValField + '?component_id=' + iComponent + '&parameter_id=' + iParameter + '&prefix=' + paramFieldPrefix + '&count=' + paramFieldCount;

  var boxParamId = 'parameter_0_0_value_box';

  /* Ajax query */
  jQuery.ajax({
    type: 'POST',
    dataType: "json",
    url: callUrl,
    timeout: 500, 
    success: function(response) {
      if (response.result.length)
      {
        jQuery('html').find('tr.param-container').each(function(key, item) {
          var ixp = jQuery(this).attr('data-parameter-id');
          var ixr = jQuery(this).attr('data-rule-id');
          var fldContainer = 'parameter_' + ixp + '_' + ixr + '_value_field_container';

          if (response.result[ixr]) {
            jQuery(this).find('#' + fldContainer).empty().html(response.result[ixr]);
          }
        });
      }
    },
    error: function(error) {},        
    complete: function() { }
  }); 
});
<!--/script-->
<?php $hlpBroker->js->endInlineJavascript(); ?>
