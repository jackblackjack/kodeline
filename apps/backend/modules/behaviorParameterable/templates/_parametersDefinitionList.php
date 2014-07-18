<?php
$supportedTypes = array_merge(
  BaseParameterableForm::getSupportedTypes(), array('decimal' => _('Вещественное число'), 'integer' => _('Целое число')));
?>
<tr>
  <td style="white-space: nowrap">
    <?php echo $schema['Translation'][$sf_user->getCulture()]['title'] ?>
  </td>
  <td>
    <?php echo _($supportedTypes[$schema['type']]) ?>
  </td>
  <td>
    <?php echo ($schema['is_require'] ? _('Да') : _('Нет')) ?>
  </td>
  <td>
    <a href="<?php echo url_for('@parameterable_component_parameter_edit?component_id=' . $schema['component_id'] . '&param_id=' . $schema['id']) ?>"><img src="/backend/images/edit_parameter.png" alt="Редактировать" title="Редактировать" border="0" /></a>
    <a class="ask" href="<?php echo url_for('@parameterable_component_parameter_delete?component_id=' . $schema['component_id'] . '&param_id=' . $schema['id']) ?>"><img src="/backend/images/trash.png" alt="Удалить параметр" title="Удалить параметр" border="0" /></a>
  </td>
</tr>