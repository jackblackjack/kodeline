<div class="mws-form-col-1-8" id="parameter_<?php echo $parameter ?>_<?php echo $condition ?>_container" data-parameter-id="<?php echo $parameter ?>" data-rule-id="<?php echo $condition ?>">
  <?php echo $form['rules'][$parameter]['conditions'][$condition]->renderHiddenFields() ?>

  <?php echo $form['rules'][$parameter]['conditions'][$condition]['compare']->renderLabel(null, array("class" => "mws-form-label")) ?>

  <div class="mws-form-item">    
    <?php echo $form['rules'][$parameter]['conditions'][$condition]['compare']->render(array()) ?>

    <?php if ($form['rules'][$parameter]['conditions'][$condition]['compare']->hasError()): ?>
    <span><?php echo $form['rules'][$parameter]['conditions'][$condition]['compare']->renderError() ?></span>
    <?php endif ?>
  </div>
</div>

<div class="mws-form-col-2-8" id="parameter_<?php echo $parameter ?>_<?php echo $condition ?>_value_field_container">
</div>

  <td>
    <?php echo $form['rules'][$parameter]['conditions'][$condition]['categories']->render(array()) ?>
  </td>
  <td>
    <?php echo $form['rules'][$parameter]['conditions'][$condition]['logic']->render(array()) ?>
  </td>
  <td>
    <?php echo $form['rules'][$parameter]['conditions'][$condition]['compare']->render(array()) ?>
  </td>