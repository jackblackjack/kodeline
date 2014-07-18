<?php if (isset($path)): ?>
<h2>Товар "<?php echo $rootNode['title'] ?>" / <?php echo $node['title'] ?></h2>
<?php endif ?>

<table id="rounded-corner">
  <thead>
    <tr>
      <th scope="col" class="rounded-company"></th>
      <th scope="col" class="rounded">Название</th>
      <th scope="col" class="rounded">Уровень</th>
      <th scope="col" class="rounded-q4">Действия</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3" class="rounded-foot-left">
        <em>Кликните на 
          <img src="/backend/images/library_item.png" alt="Раскрыть список" title="Раскрыть список" border="0" /> 
          для <strong>просмотра дочерних элементов</strong>
        </em>
        <br />
        <em>Кликните на 
          <img src="/backend/images/user_edit.png" alt="Редактировать пункт" title="Редактировать пункт" border="0" /> 
          для <strong>редактирования</strong>
        </em>
        <br />
        <em>Кликните на 
          <img src="/backend/images/plus.png" alt="Добавить пункт" title="Добавить пункт" border="0" /> 
          для <strong>добавления подкатегории или товара</strong>
        </em>
        <br />
        <em>Кликните на 
          <img src="/backend/images/trash.png" alt="Удалить пункт" title="Удалить пункт" border="0" /> 
          для <strong>удаления</strong>
        </em>
      </td>
      <td class="rounded-foot-right">&nbsp;</td>
    </tr>
  </tfoot>
  <tbody>
    <?php if (isset($node)): ?>
    <tr>
      <?php if (empty($node['parent_id'])): ?>
      <td>
        <a href="<?php echo url_for('@backend_fxshop_list_nodes') ?>">
          <img src="/backend/images/library_item.png" alt="Вернуться на уровень выше" title="Вернуться на уровень выше" border="0" />
        </a>
      </td>
      <td><a href="<?php echo url_for('@backend_fxshop_list_nodes') ?>">..</a></td>
      <?php else: ?>
      <td>
        <a href="<?php echo url_for('@backend_fxshop_list_node?id=' . $node['parent_id']) ?>">
          <img src="/backend/images/library_item.png" alt="Вернуться на уровень выше" title="Вернуться на уровень выше" border="0" />
        </a>
      </td>
      <td>
        <!--a href="<?php echo url_for('@backend_fxshop_list_node?id=' . $node['parent_id']) ?>"><?php echo $node['title'] ?></a-->
        <a href="<?php echo url_for('@backend_fxshop_list_node?id=' . $node['parent_id']) ?>">..</a>
      </td>
      <?php endif ?>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <?php endif ?>

    <?php if (isset($list) && $list->count()): ?>
    <?php foreach($list as $item): ?>
    <tr>
      <?php if ($item['is_category']): ?>
      <td>
        <a href="<?php echo url_for('@backend_fxshop_list_node?id=' . $item['id']) ?>">
          <img src="/backend/images/library_item.png" alt="Дочерних элементов: <?php echo $item['children'] ?>" title="Дочерних элементов: <?php echo $item['children'] ?>" border="0" />
        </a>
      </td>
      <td><a href="<?php echo url_for('@backend_fxshop_list_node?id=' . $item['id']) ?>"><?php echo $item['title'] ?></a></td>
      <?php else: ?>
      <td>
        <a href="<?php echo url_for('@parameterable_component_parameters?object_id=' . $item['id'] . '&model=' . $modelName) ?>">
          <img src="/backend/images/product.png" alt="Открыть свойства" title="Открыть свойства" border="0" />
        </a>
      </td>
      <td><?php echo $item['title'] ?></td>
      <?php endif ?>
      <td>
        <a href="<?php echo url_for('@backend_fxshop_list_node_position?motion=up&id=' . $item['id']) ?>"><img src="/backend/images/arrow_up.png" alt="" title="" border="0" /></a>
        <a href="<?php echo url_for('@backend_fxshop_list_node_position?motion=down&id=' . $item['id']) ?>"><img src="/backend/images/arrow_down.png" alt="" title="" border="0" /></a>
      </td>
      <td>       
        <?php if ($item['is_category']): ?>
        <a href="<?php echo url_for('@backend_fxshop_list_category_new?parent_id=' . $item['id']) ?>"><img src="/backend/images/add_subfolder.png" alt="Добавить подкатегорию" title="Добавить подкатегорию" border="0" /></a>
        <a href="<?php echo url_for('@backend_fxshop_list_category_edit?id=' . $item['id']) ?>"><img src="/backend/images/edit_item.png" alt="Редактировать" title="Редактировать" border="0" /></a>
        <?php else: ?>
        <a href="<?php echo url_for('@backend_fxshop_list_node_edit?id=' . $item['id']) ?>"><img src="/backend/images/edit_item.png" alt="Редактировать" title="Редактировать" border="0" /></a>
        <?php endif ?>
        <a href="<?php echo url_for('@backend_fxshop_list_node_delete?id=' . $item['id']) ?>" class="ask"><img src="/backend/images/trash.png" alt="Удалить" title="Удалить" border="0" /></a>
      </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
  </tbody>
</table>

<div style="align: center; position: relative; margin-top: 5px; float: left">
<?php if (isset($node)): ?>
  <?php if (false && $node['is_category']): ?>
  <a href="<?php echo url_for('@backend_fxshop_list_node_edit?id=' . $node['id']) ?>" class="bt_red">
    <span class="bt_red_lft"></span><strong>Свойства категории</strong><span class="bt_red_r"></span>
  </a>
  <?php endif ?>

  <a href="<?php echo url_for('@backend_fxshop_list_node_new?parent_id=' . $node['id']) ?>" class="bt_blue">
    <span class="bt_blue_lft"></span><strong>Добавить подкатегорию</strong><span class="bt_blue_r"></span>
  </a>

  <a href="<?php echo url_for('@backend_fxshop_list_node_new?parent_id=' . $node['id']) ?>" class="bt_green">
    <span class="bt_green_lft"></span><strong>Добавить элемент в словарь</strong><span class="bt_green_r"></span>
  </a>
<?php else: ?>
  <a href="<?php echo url_for('@backend_fxshop_list_category_new') ?>" class="bt_blue">
    <span class="bt_blue_lft"></span><strong>Добавить словарь</strong><span class="bt_blue_r"></span>
  </a>
<?php endif ?>
</div>

<!--div class="pagination">
  <span class="disabled"><< prev</span><span class="current">1</span><a href="">2</a><a href="">3</a><a href="">4</a><a href="">5</a>…<a href="">10</a><a href="">11</a><a href="">12</a>...<a href="">100</a><a href="">101</a><a href="">next >></a>
</div-->