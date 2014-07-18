<?php if (isset($path)): ?>
<h2>Товар "<?php echo $rootNode['title'] ?>" / <?php echo $node['title'] ?></h2>
<?php endif ?>

<table id="rounded-corner">
  <thead>
    <tr>
      <th scope="col" class="rounded-company"></th>
      <th scope="col" class="rounded">Название</th>
      <th scope="col" class="rounded">Подфильтров</th>
      <th scope="col" class="rounded-q4">Действия</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3" class="rounded-foot-left">
        <em>Кликните на 
          <img src="/backend/images/filter_enabled.png" alt="Список подфильтров" title="Список подфильтров" border="0" /> 
          для <strong>просмотра подфильтров</strong>
        </em>
        <br />
        <em>Кликните на 
          <img src="/backend/images/filter_edit.png" alt="Редактировать фильтр" title="Редактировать фильтр" border="0" /> 
          для <strong>редактирования фильтра</strong>
        </em>
        <br />
        <em>Кликните на 
          <img src="/backend/images/trash.png" alt="Удалить фильтр" title="Удалить фильтр" border="0" /> 
          для <strong>удаления фильтра</strong>
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
        <a href="<?php echo url_for('@backend_fxshop_filter_nodes') ?>">
          <img src="/backend/images/filter_enabled.png" alt="Вернуться на уровень выше" title="Вернуться на уровень выше" border="0" />
        </a>
      </td>
      <td><a href="<?php echo url_for('@backend_fxshop_filter_nodes') ?>">...</a></td>
      <?php else: ?>
      <td>
        <a href="<?php echo url_for('@backend_fxshop_filter_node?id=' . $node['parent_id']) ?>">
          <img src="/backend/images/filter_enabled.png" alt="Вернуться на уровень выше" title="Вернуться на уровень выше" border="0" />
        </a>
      </td>
      <td>
        <a href="<?php echo url_for('@backend_fxshop_filter_node?id=' . $node['parent_id']) ?>"><?php echo $node['title'] ?></a></td>
      <?php endif ?>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <?php endif ?>

    <?php if (isset($list) && $list->count()): ?>
    <?php foreach($list as $item): ?>
    <tr>
      <td>
        <a href="<?php echo url_for('@backend_fxshop_filter_node?id=' . $item['id']) ?>">
          <img src="/backend/images/filter_enabled.png" alt="Фильтр активен" title="Фильтр активен" border="0" />
        </a>
      </td>
      <td>
        <a href="<?php echo url_for('@backend_fxshop_filter_node?id=' . $item['id']) ?>" title="<?php echo $item['title'] ?>">
          <?php echo $item['title'] ?>
        </a>
      </td>
      <td><?php echo $item['children'] ?></td>
      <td>
        <a href="<?php echo url_for('@backend_fxshop_filter_node_edit?id=' . $item['id']) ?>"><img src="/backend/images/filter_edit.png" alt="Редактировать фильтр" title="Редактировать фильтр" border="0" /></a>
        
        <a href="<?php echo url_for('@backend_fxshop_filter_node_delete?id=' . $item['id']) ?>" class="ask"><img src="/backend/images/trash.png" alt="Удалить фильтр" title="Удалить фильтр" border="0" /></a>
      </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
  </tbody>
</table>

<?php if (isset($node)): ?>
<a href="<?php echo url_for('@backend_fxshop_filter_node_edit?id=' . $node['id']) ?>" class="bt_red">
  <span class="bt_red_lft"></span><strong>Редактировать <?php echo $node['title'] ?></strong><span class="bt_red_r"></span>
</a>

<a href="<?php echo url_for('@backend_fxshop_filter_node_new?parent_id=' . $node['id']) ?>" class="bt_green">
  <span class="bt_green_lft"></span><strong>Добавить новый подфильтр</strong><span class="bt_green_r"></span>
</a>
<?php else: ?>
<?php if (isset($list) && ! $list->count()): ?>
<strong>Пока ни одного фильтра</strong>
<?php endif ?>

<div style="align: center; position: relative; margin-top: 5px; float: left">
  <a href="<?php echo url_for('@backend_fxshop_filter_node_new') ?>" class="bt_green">
    <span class="bt_green_lft"></span><strong>Добавить фильтр</strong><span class="bt_green_r"></span>
  </a>
</div>
<?php endif ?>

<!--div class="pagination">
  <span class="disabled"><< prev</span><span class="current">1</span><a href="">2</a><a href="">3</a><a href="">4</a><a href="">5</a>…<a href="">10</a><a href="">11</a><a href="">12</a>...<a href="">100</a><a href="">101</a><a href="">next >></a>
</div-->