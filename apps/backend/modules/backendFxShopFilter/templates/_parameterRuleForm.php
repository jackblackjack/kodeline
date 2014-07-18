<tr id="parameter_<?php echo $parameter ?>_<?php echo $condition ?>_container" data-parameter-id="<?php echo $parameter ?>" data-rule-id="<?php echo $condition ?>" class="param-container">
  <?php echo $form['rules'][$parameter]['conditions'][$condition]->renderHiddenFields() ?>
  <td>
    <?php echo $form['rules'][$parameter]['conditions'][$condition]['categories']->render(array()) ?>
  </td>
  <td>
    <?php echo $form['rules'][$parameter]['conditions'][$condition]['logic']->render(array()) ?>
  </td>
  <td>
    <?php echo $form['rules'][$parameter]['conditions'][$condition]['compare']->render(array()) ?>
  </td>
  <td id="parameter_<?php echo $parameter ?>_<?php echo $condition ?>_value_field_container">
  </td>
</tr>