<tr>
  <td class="checkbox-column">
    <input type="checkbox" name="checked" value="<?php echo $schema['id'] ?>" />
  </td>
  <td>
    <?php echo $schema['Translation'][$sf_user->getCulture()]['title'] ?>

    <span class="btn-group">
      <a href="<?php echo url_for('@parameterable_component_parameter_edit?component_id=' . $schema['component_id'] . '&param_id=' . $schema['id']) ?>" class="btn btn-small" title="Редактировать только метку поля">
        <i class="icon-pencil"></i>
      </a>
    </span>
  </td>
  <td>
    <?php echo $schema['name'] ?>

    <span class="btn-group">
      <a href="<?php echo url_for('@parameterable_component_parameter_edit?component_id=' . $schema['component_id'] . '&param_id=' . $schema['id']) ?>" class="btn btn-small" title="Редактировать только название поля">
        <i class="icon-pencil"></i>
      </a>
    </span>
  </td>
  <td>
    <a href="#" class="btn dropdown-toggle" data-toggle="dropdown">
      <?php echo $schema['type'] ?> (<?php echo $schema['length'] ?>) <span class="caret"></span>
    </a>
    
    <ul class="dropdown-menu pull-right">
        <li><a href="#"><i class="icol-disconnect"></i> Disconnect From Server</a></li>
        <li class="divider"></li>
        <li class="dropdown-submenu">
            <a href="#">More Options</a>
            <ul class="dropdown-menu">
                <li><a href="#">Contact Administrator</a></li>
                <li><a href="#">Destroy Table</a></li>
            </ul>
        </li>
    </ul>
  </td>
  <td>      
    <span class="btn-group">
      <a href="<?php echo url_for('@parameterable_component_parameter_edit?component_id=' . $schema['component_id'] . '&param_id=' . $schema['id']) ?>" class="btn btn-small" title="Редактировать поле">
        <i class="icon-pencil"></i>
      </a>
      <a href="<?php echo url_for('@parameterable_component_parameter_delete?component_id=' . $schema['component_id'] . '&param_id=' . $schema['id']) ?>" class="btn btn-small ask" title="Удалить поле">
        <i class="icon-trash"></i>
      </a>
    </span>
  </td>
</tr>