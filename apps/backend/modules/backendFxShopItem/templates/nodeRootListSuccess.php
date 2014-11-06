<h2>Типы содержания сайта</h2>

<div class="mws-panel grid_6">

  <div class="mws-panel-header">
    <span><i class="icon-table"></i> Типы содержания сайта</span>
  </div>

  <div class="mws-panel-toolbar">
      <div class="btn-toolbar">
          <div class="btn-group">
              <a href="<?php echo url_for('@backend_product_category_new') ?>" class="btn"><i class="icol-add"></i> Новый тип</a>
              <a href="#" class="btn"><i class="icol-cross"></i> Reject</a>
              <a href="#" class="btn"><i class="icol-printer"></i> Print</a>
              <a href="#" class="btn"><i class="icol-arrow-refresh"></i> Renew</a>
              <a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
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
          </div>
      </div>
  </div>

  <div class="mws-panel-body no-padding">
    <table class="mws-table">
      <thead>
        <tr>
          <th class="checkbox-column">
            <input type="checkbox">
          </th>
          <th>Имя типа</th>
          <th>Дочерних типов</th>
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

        <?php if (isset($list) && $list->count()): ?>
        <?php foreach($list as $item): ?>
        <tr>
          <?php if ($item['is_category']): ?>
          <td class="checkbox-column">
            <input type="checkbox" name="checked" value="<?php echo $item['id'] ?>" />
          </td>

          <td>
            <a href="<?php echo url_for('@backend_product_item_node?id=' . $item['id']) ?>">
              <?php echo $item['title'] ?>
            </a>
          </td>

          <td><?php echo $item['children'] ?></td>

          <?php else: ?>
          <td>
            <a href="<?php echo url_for('@parameterable_component_parameters?object_id=' . $item['id'] . '&model=' . $modelName) ?>">
              <img src="/backend/images/product.png" alt="Открыть свойства" title="Открыть свойства" border="0" />
            </a>
          </td>
          <td><?php echo $item['title'] ?></td>
          <?php endif ?>

          <td>      
            <span class="btn-group">
              <a href="<?php echo url_for('@backend_product_item_node_position?motion=up&id=' . $item['id']) ?>" class="btn btn-small"><i class="icon-circle-arrow-up"></i></a>
              <a href="<?php echo url_for('@backend_product_item_node_position?motion=down&id=' . $item['id']) ?>" class="btn btn-small"><i class="icon-circle-arrow-down"></i></a>

              <?php if ($item['is_category']): ?>
              <a href="<?php echo url_for('@backend_product_category_new?parent_id=' . $item['id']) ?>" class="btn btn-small" title="Добавить подкатегорию"><i class="icon-github-4"></i></a>
              <a href="<?php echo url_for('@backend_product_category_edit?id=' . $item['id']) ?>" class="btn btn-small" title="Редактировать"><i class="icon-pencil"></i></a>
              <?php else: ?>
              <a href="<?php echo url_for('@backend_product_item_node_edit?id=' . $item['id']) ?>" class="btn btn-small" title="Редактировать"><i class="icon-pencil"></i></a>
              <?php endif ?>

              <a href="<?php echo url_for('@parameterable_component_parameters?object_id=' . $item['id'] . '&model=' . $modelName) ?>" class="btn btn-small" title="Поля типа"><i class="icon-cogs"></i></a>
              <a href="<?php echo url_for('@backend_product_item_node_delete?id=' . $item['id']) ?>" class="btn btn-small" title="Удалить тип"><i class="icon-trash ask"></i></a>
            </span>
          </td>
        </tr>
        <?php endforeach ?>
        <?php endif ?>
      </tbody>
    </table>
  </div>
<!--/div-->

  <!--div style="align: center; position: relative; margin-top: 5px; float: left">
  <?php if (isset($node)): ?>
    <?php if (false && $node['is_category']): ?>
    <a href="<?php echo url_for('@backend_product_category_edit?id=' . $node['id']) ?>" class="bt_red">
      <span class="bt_red_lft"></span><strong>Свойства категории</strong><span class="bt_red_r"></span>
    </a>
    <?php endif ?>

    <a href="<?php echo url_for('@backend_product_category_new?parent_id=' . $node['id']) ?>" class="bt_blue">
      <span class="bt_blue_lft"></span><strong>Добавить подкатегорию</strong><span class="bt_blue_r"></span>
    </a>

    <a href="<?php echo url_for('@backend_product_item_node_new?parent_id=' . $node['id']) ?>" class="bt_green">
      <span class="bt_green_lft"></span><strong>Добавить новый объект</strong><span class="bt_green_r"></span>
    </a>
  <?php else: ?>
    <a href="<?php echo url_for('@backend_product_category_new') ?>" class="bt_blue">
      <span class="bt_blue_lft"></span><strong>Добавить категорию</strong><span class="bt_blue_r"></span>
    </a>
  <?php endif ?>
  </div-->

  <!--div class="pagination">
    <span class="disabled"><< prev</span><span class="current">1</span><a href="">2</a><a href="">3</a><a href="">4</a><a href="">5</a>…<a href="">10</a><a href="">11</a><a href="">12</a>...<a href="">100</a><a href="">101</a><a href="">next >></a>
  </div-->
</div>

<div class="mws-panel grid_2 mws-collapsible">
  <div class="mws-panel-header">
    <span>Краткая помощь</span>
    <div class="mws-collapse-button mws-inset"><span></span></div>
  </div>

  <div class="mws-panel-inner-wrap" style="display: block;">
    <div class="mws-panel-body">
              <tfoot>
            <tr>
              <td colspan="3" class="rounded-foot-left">
                <em>Кликните на 
                  <img src="/backend/images/closed_folder.png" alt="Элементы и подкатегории" title="Элементы и подкатегории" border="0" /> 
                  для <strong>просмотра элементов и подкатегорий</strong>
                </em>
                <br />
                <em>Кликните на 
                  <img src="/backend/images/edit_item.png" alt="Редактировать свойства" title="Редактировать свойства" border="0" /> 
                  для <strong>просмотра или редактирования свойств</strong>
                </em>
                <br />
                <em>Кликните на 
                  <img src="/backend/images/add_subfolder.png" alt="Добавить подкатегорию" title="Добавить подкатегорию" border="0" /> 
                  для <strong>добавления подкатегории</strong>
                </em>
                <br />
                <em>Кликните на 
                  <img src="/backend/images/trash.png" alt="Удалить элемент" title="Удалить элемент" border="0" /> 
                  для <strong>удаления</strong>
                </em>
              </td>
              <td class="rounded-foot-right">&nbsp;</td>
            </tr>
          </tfoot>
    </div>
  </div>

  <div class="mws-panel-header">
    <span>Что такое тип содержания?</span>
                    <div class="mws-collapse-button mws-inset"><span></span></div></div>
                    <div class="mws-panel-inner-wrap" style="display: block;"><div class="mws-panel-body">
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque non leo convallis nibh tristique commodo. Cras tincidunt hendrerit pharetra. Etiam erat magna, egestas sed placerat at, congue sed nisi. Nullam eget varius leo. Integer at justo a velit imperdiet pulvinar. Sed magna mi, sodales sit amet aliquet ac, eleifend eget sem. Nam ipsum lectus, fringilla sed rutrum ac, tempus in orci. Pellentesque condimentum dui a elit rutrum at posuere tellus dignissim. Aliquam erat volutpat. Suspendisse potenti. Sed convallis convallis tellus, id volutpat leo euismod in. Curabitur dapibus commodo vehicula. Nullam varius, lacus at porta pellentesque, dolor massa rutrum lorem, vehicula dapibus dui erat nec mi. Donec condimentum lectus ut ligula condimentum et luctus orci pharetra. Fusce semper tempor dui, vitae sollicitudin mauris volutpat in. Aliquam erat volutpat.</p>
                    </div></div>
</div>