<?php include_partial('global/block/metas', array('metas' => array('title' => "Список пользователей"))); ?>

<div class="mws-panel grid_8">
  <div class="mws-panel-header">
    <span><i class="icon-table"></i> Список пользователей</span>
  </div>
  <div class="mws-panel-toolbar">
    <div class="btn-toolbar">
      <div class="btn-group">
        <a href="<?php echo url_for('@backend_user_new') ?>" class="btn"><i class="icol-add"></i> Добавить пользователя</a>
        <a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
        <ul class="dropdown-menu pull-right">
          <li><a href="<?php echo url_for('@parameterable_component?model=klUser') ?>"><i class="icol-disconnect"></i> Пользовательские поля</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="mws-panel-body no-padding">
    <table class="mws-datatable-fn mws-table">
      <thead>
        <tr>
          <th class="checkbox-column"><input type="checkbox"></th>
          <th>Логин</th>
          <th>Активный?</th>
          <th>Время создания</th>
          <th>Время последнего входа</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php if (isset($items_pager) && $items_pager->count()): ?>
          <?php foreach($items_pager->getResults() as $item): ?>
          <tr>
            <td class="checkbox-column">
              <input type="checkbox">
            </td>
            <td><?php echo $item['username'] ?></td>
            <td><?php echo $item['is_active'] ?></td>
            <td><?php echo $item['created_at'] ?></td>
            <td><?php echo $item['last_login'] ?></td>
            <td>       
              <a href="<?php echo url_for('@backend_user_edit?id_user=' . $item['id']) ?>"><img src="/backend/images/edit_item.png" alt="Редактировать" title="Редактировать" border="0" /></a>
              <a href="<?php echo url_for('@backend_user_delete?id_user=' . $item['id']) ?>" class="ask"><img src="/backend/images/trash.png" alt="Удалить" title="Удалить" border="0" /></a>
            </td>
          </tr>
          <?php endforeach ?>
        <?php endif ?>
      </tbody>
    </table>
  </div>
</div>

<a href="<?php echo url_for('@parameterable_component?model=klUser') ?>">Поля пользователей</a>

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
