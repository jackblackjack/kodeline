<tr>
  <td></td>
  <td><?php echo $schema['name'] ?></td>
  <td><?php echo $schema['type'] ?>(<?php echo $schema['length'] ?>)</td>
  <td><?php echo $schema['Translation'][$sf_user->getCulture()]['title'] ?></td>
  <td>
    <a href="<?php echo url_for('@parameterable_component_parameter_edit?component_id=' . $schema['component_id'] . '&param_id=' . $schema['id']) ?>">
      <img src="/backend/images/user_edit.png" alt="Редактировать" title="Редактировать" border="0" />
    </a>

    <a class="ask" href="<?php echo url_for('@parameterable_component_parameter_delete?component_id=' . $schema['component_id'] . '&param_id=' . $schema['id']) ?>">
      <img src="/backend/images/trash.png" alt="Удалить параметр" title="Удалить параметр" border="0" />
    </a>
  </td>
</tr>