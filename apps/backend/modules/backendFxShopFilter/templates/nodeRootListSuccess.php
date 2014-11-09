<div class="mws-panel grid_6">

  <div class="mws-panel-header">
    <span><i class="icon-table"></i> Фильтры содержания сайта</span>
  </div>

  <div class="mws-panel-toolbar">
      <div class="btn-toolbar">
          <div class="btn-group">
              <a href="<?php echo url_for('@backend_fxshop_filter_node_new') ?>" class="btn"><i class="icol-add"></i> Новый фильтр</a>
              <a href="#" class="btn"><i class="icol-cross"></i> Удалить</a>
              <a href="#" class="btn"><i class="icol-printer"></i> Выделить все</a>
              <a href="#" class="btn"><i class="icol-arrow-refresh"></i> Еще</a>
              <a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
              <ul class="dropdown-menu pull-right">
                  <li><a href="#"><i class="icol-disconnect"></i> Создать фильтр</a></li>
                  <li class="divider"></li>
                  <li class="dropdown-submenu">
                      <a href="#">More Options</a>
                      <ul class="dropdown-menu">
                          <li><a href="#">Contact Administrator</a></li>
                          <li><a href="#">Destroy Table</a></li>
                      </ul>
                  </li>
              </ul>
          </div>
      </div>
  </div>

  <?php if (isset($list) && $list->count()): ?>
  <div class="mws-panel-body no-padding">
    <table class="mws-table">
      <thead>
        <tr>
          <th class="checkbox-column">
            <input type="checkbox">
          </th>
          <th>Название</th>
          <th>Заметка</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
      <?php if (isset($node)): ?>
      <tr>
        <?php if (empty($node['parent_id'])): ?>
        <td>
          <a href="<?php echo url_for('@backend_product_item_nodes') ?>">
            <img src="/backend/images/open_folder.png" alt="Вернуться на уровень выше" title="Вернуться на уровень выше" border="0" />
          </a>
        </td>
        <td><a href="<?php echo url_for('@backend_product_item_nodes') ?>">..</a></td>
        <?php else: ?>
        <td>
          <a href="<?php echo url_for('@backend_product_item_node?id=' . $node['parent_id']) ?>">
            <img src="/backend/images/open_folder.png" alt="Вернуться на уровень выше" title="Вернуться на уровень выше" border="0" />
          </a>
        </td>
        <td>
          <!--a href="<?php echo url_for('@backend_product_item_node?id=' . $node['parent_id']) ?>"><?php echo $node['title'] ?></a-->
          <a href="<?php echo url_for('@backend_product_item_node?id=' . $node['parent_id']) ?>">..</a>
        </td>
        <?php endif ?>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <?php endif ?>

        <?php foreach($list as $item): ?>
        <tr>
          <td class="checkbox-column">
            <input type="checkbox" name="checked" value="<?php echo $item['id'] ?>" />
          </td>
          <td><?php echo $item['title'] ?></td>
          <td><?php echo $item['annotation'] ?></td>

          <td>      
            <span class="btn-group">
              <a href="<?php echo url_for('@backend_fxshop_filter_node?id=' . $item['id']) ?>" class="btn btn-small" title="Получить результат"><i class="icon-play"></i></a>
              <a href="#" class="btn btn-small"><i class="icon-circle-arrow-up"></i></a>
              <a href="#" class="btn btn-small"><i class="icon-circle-arrow-down"></i></a>

              <a href="<?php echo url_for('@backend_fxshop_filter_node_edit?id=' . $item['id']) ?>" class="btn btn-small" title="Редактировать фильтр"><i class="icon-pencil"></i></a>
              <a href="<?php echo url_for('@backend_fxshop_filter_node_delete?id=' . $item['id']) ?>" class="btn btn-small" title="Удалить фильтр"><i class="icon-trash ask"></i></a>
            </span>
          </td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
  <?php endif ?>
</div>
