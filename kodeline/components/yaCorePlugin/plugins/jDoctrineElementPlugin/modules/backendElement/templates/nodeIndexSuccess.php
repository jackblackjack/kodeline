<?php if (isset($rootNode)): ?>
<h2>Прайс-лист "<?php echo $rootNode['title'] ?>" / <?php echo $node['title'] ?></h2>
<?php else: ?>
<h2>Товар "<?php echo $node['title'] ?>"</h2>
<?php endif ?>

<table id="rounded-corner">
  <thead>
    <tr>
      <!--th scope="col" class="rounded-company"></th-->
      <th scope="col" class="rounded-company"></th>
      <th scope="col" class="rounded">Название</th>
      <th scope="col" class="rounded">Уровень</th>
      <th scope="col" class="rounded">Действия</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="4" class="rounded-foot-left">
        <em>Кликните на <img src="/backend/images/expand_tree.png" alt="Раскрыть список" title="Раскрыть список" border="0" /> для <strong>просмотра вложенных пунктов</strong></em>
        <br /><em>Кликните на <img src="/backend/images/user_edit.png" alt="Редактировать пункт" title="Редактировать пункт" border="0" /> для <strong>редактирования названия пункта прайс-листа</strong></em>
        <br /><em>Кликните на <img src="/backend/images/plus.png" alt="Добавить пункт" title="Добавить пункт" border="0" /> для <strong>добавления нового пункта прайс-листа</strong></em>
        <br /><em>Кликните на <img src="/backend/images/trash.png" alt="Удалить пункт" title="Удалить пункт" border="0" /> для <strong>удаления пункта прайс-листа</strong></em>
      </td>
      <td class="rounded-foot-right">&nbsp;</td>
    </tr>
  </tfoot>
  <tbody>
    <?php foreach($children as $child): ?>
    <tr>
      <!--td><input type="checkbox" name="node_<?php echo $child['id'] ?>" /></td-->
      <td>
        <?php if ($child['children']): ?>
        <a href="<?php echo url_for('@backend_goods_node?id=' . $child['id']) ?>">
          <img src="/backend/images/expand_tree.png" alt="Раскрыть (элементов: <?php echo $child['children'] ?>)" title="Раскрыть (элементов: <?php echo $child['children'] ?>)" border="0" />
        </a>
        <?php else: ?>
        &nbsp;
        <?php endif ?>
      </td>
      <td><?php echo $child['title'] ?></td>
      <td>
        <a href="<?php echo url_for('@backend_goods_node_position?motion=up&id=' . $child['id']) ?>">
          <img src="/backend/images/arrow_up.png" alt="" title="" border="0" />
        </a>
        <a href="<?php echo url_for('@backend_goods_node_position?motion=down&id=' . $child['id']) ?>">
          <img src="/backend/images/arrow_down.png" alt="" title="" border="0" />
        </a>
      </td>
      <td>
        <a href="<?php echo url_for('@backend_goods_node_new?parent_id=' . $child['id']) ?>">
          <img src="/backend/images/plus.png" alt="Редактировать" title="Редактировать" border="0" />
        </a>
        <a href="<?php echo url_for('@backend_goods_node_new?parent_id=' . $child['id']) ?>">
          <img src="/backend/images/plus.png" alt="Добавить пункт" title="Добавить пункт" border="0" />
        </a>
        <a href="<?php echo url_for('@backend_goods_node_edit?id=' . $child['id']) ?>"><img src="/backend/images/user_edit.png" alt="" title="" border="0" /></a>
        <a href="<?php echo url_for('@backend_goods_node_delete?id=' . $child['id']) ?>" class="ask"><img src="/backend/images/trash.png" alt="" title="" border="0" /></a>       
      </td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>

<a href="<?php echo url_for('@backend_goods_node_edit?id=' . $node['id']) ?>" class="bt_red">
  <span class="bt_red_lft"></span><strong>Редактировать <?php echo $node['title'] ?></strong><span class="bt_red_r"></span>
</a>

<a href="<?php echo url_for('@backend_goods_node_new?parent_id=' . $node['id']) ?>" class="bt_green">
  <span class="bt_green_lft"></span><strong>Добавить новый пункт в <?php echo $node['title'] ?></strong><span class="bt_green_r"></span>
</a>

 

<!--div class="pagination">
  <span class="disabled"><< prev</span><span class="current">1</span><a href="">2</a><a href="">3</a><a href="">4</a><a href="">5</a>…<a href="">10</a><a href="">11</a><a href="">12</a>...<a href="">100</a><a href="">101</a><a href="">next >></a>
</div-->