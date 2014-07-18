<?php include_partial('global/block/metas', array('metas' => array('title' => "Список пользователей"))); ?>
<?php $hlpBroker->js->beginInlineJavascript(yaWebResponse::LOCATION_BODY) ?>
/*
ddaccordion.init({headerclass: "detail-block-header", contentclass: "detail-block", revealtype: "click", collapseprev: false, onemustopen: false, animatedefault: false, persiststate: true, toggleclass: ["detail-block-collapse", "detail-block-expand"], animatespeed: "fast"});
*/
<?php $hlpBroker->js->endInlineJavascript() ?>

<h2>Список сайтов</h2>

      versionable_value: 'kodeline-site'
  columns:
    id:             { type: integer(5), primary: true, autoincrement: true, unsigned: true }
    name:           { type: clob(16777215), notnull: true }
    annotation:     { type: clob, notnull: false, default: null }
    is_active:      { type: integer(1), notnull: true, default: 1 }

<table id="rounded-corner">
  <thead>
    <tr>
      <th scope="col" class="rounded-company"></th>
      <th scope="col" class="rounded">Название</th>
      <th scope="col" class="rounded">Описание</th>
      <th scope="col" class="rounded">Активный?</th>
      <th scope="col" class="rounded">Создан</th>
      <th scope="col" class="rounded-q4">Действия</th>
    </tr>
  </thead>
  <tbody>
    <?php if (isset($items_pager) && $items_pager->count()): ?>
      <?php foreach($items_pager->getResults() as $item): ?>
      <tr class="detail-block-header">
        <td class="statusicon">&nbsp;</td>
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

<a href="<?php echo url_for('@parameterable_component?model=klDomains') ?>">Поля пользователей</a>
<a href="<?php echo url_for('@backend_klsite_new') ?>">Добавить пользователя</a>

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
