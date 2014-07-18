    <tr>
      <td></td>
      <td>
        <?php echo $schema['Translation'][$sf_user->getCulture()]['title'] ?>
      </td>
      <td>
        <?php echo $object[$schema['name']] ?>
      </td>
      <td>
        <a href="<?php echo url_for('@parameterable_component_parameter_value_edit?model=' . $modelName . '&object_id=' . $object['id'] . '&param_id=' . $schema['id']) ?>">
          <img src="/backend/images/user_edit.png" alt="Редактировать" title="Редактировать" border="0" />
        </a>

        <!--a href="<?php echo url_for('@parameterable_component_parameter_edit?component_id=' . $schema['component_id'] . '&param_id=' . $schema['id']) ?>">
          <img src="/backend/images/plus.png" alt="Изменить тип параметра" title="Изменить тип параметра" border="0" />
        </a-->

        <!--a href="" class="ask">
          <img src="/backend/images/trash.png" alt="Обнулить?" title="Обнулить?" border="0" />
        </a-->
      </td>
    </tr>