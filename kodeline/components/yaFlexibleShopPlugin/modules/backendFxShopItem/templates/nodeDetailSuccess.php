<?php if (isset($path)): ?>
<h2>Товар "<?php echo $rootNode['title'] ?>" / <?php echo $node['title'] ?></h2>
<?php endif ?>

<table id="rounded-corner">
  <thead>
    <tr>
      <th scope="col" class="rounded-company"></th>
      <th scope="col" class="rounded">Название</th>
      <th scope="col" class="rounded">Уровень</th>
      <th scope="col" class="rounded">Действия</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3" class="rounded-foot-left">
        <em>Кликните на 
          <img src="/backend/images/expand_tree.png" alt="Раскрыть список" title="Раскрыть список" border="0" /> 
          для <strong>просмотра вложенных пунктов</strong>
        </em>
        <br />
        <em>Кликните на 
          <img src="/backend/images/user_edit.png" alt="Редактировать пункт" title="Редактировать пункт" border="0" /> 
          для <strong>редактирования названия пункта товаров</strong>
        </em>
        <br />
        <em>Кликните на 
          <img src="/backend/images/plus.png" alt="Добавить пункт" title="Добавить пункт" border="0" /> 
          для <strong>добавления нового пункта в товары</strong>
        </em>
        <br />
        <em>Кликните на 
          <img src="/backend/images/trash.png" alt="Удалить пункт" title="Удалить пункт" border="0" /> 
          для <strong>удаления пункта</strong>
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
        <a href="<?php echo url_for('@backend_product_item_nodes') ?>">
          <img src="/backend/images/open_folder.png" alt="Вернуться на уровень выше" title="Вернуться на уровень выше" border="0" />
        </a>
      </td>
      <td><a href="<?php echo url_for('@backend_product_item_nodes') ?>">...</a></td>
      <?php else: ?>
      <td>
        <a href="<?php echo url_for('@backend_product_item_node?id=' . $node['parent_id']) ?>">
          <img src="/backend/images/open_folder.png" alt="Вернуться на уровень выше" title="Вернуться на уровень выше" border="0" />
        </a>
      </td>
      <td>
        <a href="<?php echo url_for('@backend_product_item_node?id=' . $node['parent_id']) ?>"><?php echo $node['title'] ?></a></td>
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
        <a href="<?php echo url_for('@backend_product_item_node?id=' . $item['id']) ?>">
          <img src="/backend/images/closed_folder.png" alt="Дочерних элементов: <?php echo $item['children'] ?>" title="Дочерних элементов: <?php echo $item['children'] ?>" border="0" />
        </a>
      </td>
      <td><a href="<?php echo url_for('@backend_product_item_node?id=' . $item['id']) ?>"><?php echo $item['title'] ?></a></td>
      <?php else: ?>
      <td>
        <a href="<?php echo url_for('@parameterable_component_parameters?object_id=' . $item['id'] . '&model=' . $modelName) ?>">
          <img src="/backend/images/product.png" alt="Открыть свойства" title="Открыть свойства" border="0" />
        </a>
      </td>
      <td>
        <a href="<?php echo url_for('@parameterable_component_parameters?object_id=' . $item['id'] . '&model=' . $modelName) ?>">
          <?php echo $item['title'] ?>
        </a>
      </td>
      <?php endif ?>
      <td>
        <a href="<?php echo url_for('@backend_product_item_node_position?motion=up&id=' . $item['id']) ?>">
          <img src="/backend/images/arrow_up.png" alt="" title="" border="0" />
        </a>
        <a href="<?php echo url_for('@backend_product_item_node_position?motion=down&id=' . $item['id']) ?>">
          <img src="/backend/images/arrow_down.png" alt="" title="" border="0" />
        </a>
      </td>
      <td>
        
        <?php if ($item['is_category']): ?>
        <a href="<?php echo url_for('@backend_product_item_node_new?parent_id=' . $item['id']) ?>">
          <img src="/backend/images/plus.png" alt="Добавить пункт" title="Добавить пункт" border="0" />
        </a>
        <a href="<?php echo url_for('@backend_product_category_edit?id=' . $item['id']) ?>">
          <img src="/backend/images/user_edit.png" alt="Редактировать" title="Редактировать" border="0" />
        </a>
        <?php else: ?>
        <a href="<?php echo url_for('@parameterable_component_parameters?object_id=' . $item['id'] . '&model=' . $modelName) ?>">
          <img src="/backend/images/user_edit.png" alt="Редактировать" title="Редактировать" border="0" />
        </a>
        <?php endif ?>

        <a href="<?php echo url_for('@backend_product_item_node_delete?id=' . $item['id']) ?>" class="ask">
          <img src="/backend/images/trash.png" alt="Удалить" title="Удалить" border="0" />
        </a>
      </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
  </tbody>
</table>

<?php if (isset($node)): ?>
<a href="<?php echo url_for('@backend_product_item_node_edit?id=' . $node['id']) ?>" class="bt_red">
  <span class="bt_red_lft"></span><strong>Редактировать <?php echo $node['title'] ?></strong><span class="bt_red_r"></span>
</a>

<a href="<?php echo url_for('@backend_product_item_node_new?parent_id=' . $node['id']) ?>" class="bt_green">
  <span class="bt_green_lft"></span><strong>Добавить новый пункт в <?php echo $node['title'] ?></strong><span class="bt_green_r"></span>
</a>
<?php else: ?>
<strong>Пока здесь ничего нет. Вы можете добавить новый товар или категорию.</strong>

<div style="align: center; position: relative; margin-top: 5px; float: left">
  <a href="<?php echo url_for('@parameterable_component?model=Product') ?>" class="bt_green">
    <span class="bt_green_lft"></span><strong>Параметры товаров</strong><span class="bt_green_r"></span>
  </a>

  <a href="<?php echo url_for('@backend_product_item_node_new') ?>" class="bt_green">
    <span class="bt_green_lft"></span><strong>Добавить товар</strong><span class="bt_green_r"></span>
  </a>

  <a href="<?php echo url_for('@backend_product_category_new') ?>" class="bt_green">
    <span class="bt_green_lft"></span><strong>Добавить категорию</strong><span class="bt_green_r"></span>
  </a>
</div>
<?php endif ?>


 

<!--div class="pagination">
  <span class="disabled"><< prev</span><span class="current">1</span><a href="">2</a><a href="">3</a><a href="">4</a><a href="">5</a>…<a href="">10</a><a href="">11</a><a href="">12</a>...<a href="">100</a><a href="">101</a><a href="">next >></a>
</div-->