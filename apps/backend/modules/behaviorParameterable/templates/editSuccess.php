<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<h2>Параметр "<?php echo $object['title'] ?>": редактирование</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>
    <fieldset id="form-select-type" style="padding-bottom: 0px; margin-bottom: 0px">
      <dl>
        <dt><?php echo $form['type']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['type']->render() ?>
          <span class="help"><?php echo $form['type']->renderHelp() ?></span>

          <?php if ($form['type']->hasError()): ?>
          <span><?php echo $form['type']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>
    </fieldset>

    <fieldset id="form-fields" style="padding-top: 0px; margin-top: 0px"></fieldset>

    <fieldset>
      <div class="submit">
        <input type="submit" value="Сохранить" />
      </div>                    
    </fieldset>
  </form>
</div>

<script type="text/javascript">
/* <![CDATA[ */
(function() {
  jQuery(document).ready(function() {
    var selector_id = '#<?php echo $form['type']->renderId() ?>', selector = jQuery(selector_id), fieldset = jQuery('#form-fields');

    selector.bind('change', function(event){
      fieldset.empty();
      jQuery.get(location.href, { type: jQuery(this).val() }, jQuery.proxy(function(data) {
          if (data.error) { return false; }

          // Collect inline scripts.
          var scripts = [];
          if (jQuery(data.f).find("script").length) {
            jQuery(data.f).find("script").each(function() { if ('text/javascript' == jQuery(this).attr('type')) { scripts.push(jQuery(this).text()); } });
          }

          // Include stylesheets and javascripts.
          var medialist = jQuery.merge(data.s, data.j);
          if (medialist.length) { head.load(medialist, function(){ eval(scripts.join("\n")); }); }
          else { eval(scripts.join("\n")); }

          fieldset.html(data.f);
        },this), 'json');
    });

    selector.trigger('change');
  });
})();
/* ]]> */
</script>