<?php include_partial('global/block/metas', array('metas' => array('title' => "Список групп пользователей"))); ?>
<h2>Список групп пользователей</h2>

<table id="rounded-corner">
  <thead>
    <tr>
      <th scope="col" class="rounded-company"></th>
      <th scope="col" class="rounded">Наименование</th>
      <th scope="col" class="rounded">Описание</th>
      <th scope="col" class="rounded">Пользователи</th>
      <th scope="col" class="rounded">Разрешения</th>
      <th scope="col" class="rounded-q4">Действия</th>
    </tr>
  </thead>
  <tbody>
    <?php if (isset($items_pager) && $items_pager->count()): ?>
      <?php foreach($items_pager->getResults() as $item): ?>
      <tr class="detail-block-header">
        <td class="statusicon">&nbsp;</td>
        <td><?php echo $item['name'] ?></td>
        <td><?php echo $item['description'] ?></td>
        <td><?php echo $item['name'] ?></td>
        <td><?php echo $item['name'] ?></td>
        <td>       
          <a href="<?php echo url_for('@backend_user_group_edit?id_usergroup=' . $item['id']) ?>">
            <img src="/backend/images/edit_item.png" alt="Редактировать группу" title="Редактировать группу" border="0" />
          </a>
          <a href="<?php echo url_for('@backend_user_group_delete?id_usergroup=' . $item['id']) ?>" class="ask">
            <img src="/backend/images/trash.png" alt="Удалить группу" title="Удалить группу" border="0" />
          </a>
        </td>
      </tr>
      <?php endforeach ?>
    <?php endif ?>
  </tbody>
</table>

<a href="<?php echo url_for('@parameterable_component?model=klPermission') ?>">Поля пользовательских групп</a>
<a href="<?php echo url_for('@backend_permission_new') ?>">Добавить пользовательскую группу</a>

<?php if ($items_pager->haveToPaginate()): ?>
<div class="pagination">
  <?php if (! $items_pager->isFirstPage()): ?>
  <span class="disabled"><< пред</span>
  <?php endif ?>

  <?php foreach ($items_pager->getLinks() as $pager_page): ?>
    <?php if ($pager_page == $items_pager->getPage()): ?>
      <span class="current"><?php echo $pager_page ?></span>
    <?php else: ?>
      <a href="<?php echo url_for2_escaped($sf_context->getRouting()->getCurrentRouteName(), $sf_request->getMergedParameters(sfRequest::GET, array('upage' => $pager_page))) ?>"><?php echo $pager_page ?></a>
    <?php endif ?>
  <?php endforeach ?>

  <?php if (! $pager_page->isLastPage()): ?>
  <a href="">след >></a>
  <?php endif ?>
</div>
<?php endif ?>
