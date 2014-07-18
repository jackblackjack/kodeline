<?php foreach($roots as $root): ?>
<a class="menuitem submenuheader" href="#"><?php echo $root['title'] ?></a>
<div class="submenu">
  <ul>
    <li>
      <a href="<?php echo url_for('@backend_product_category_edit?id=' . $root['id']) ?>">
        <img src="/backend/images/plus_11x11.gif" alt="Редактировать категорию" title="Редактировать" border="0" />
        Редактировать
      </a>
    <li>
    <li>
      <a href="<?php echo url_for('@parameterable_component_parameters?object_id=' . $root['id'] . '&model=' . $className) ?>">
        <img src="/backend/images/plus_11x11.gif" alt="Список параметров элемента" title="Список параметров элемента" border="0" />
        Поля объектов
      </a>
    <li>
      <a href="<?php echo url_for('@backend_product_category_new?parent_id=' . $root['id']) ?>">
        <img src="/backend/images/plus_11x11.gif" alt="Добавить категорию" title="Добавить категорию" border="0" />
        Добавить группу
      </a>
    </li>
    <li>
      <a href="<?php echo url_for('@backend_product_item_node?id=' . $root['id']) ?>">
        <img src="/backend/images/plus_11x11.gif" alt="Список объектов" title="Список объектов" border="0" />
        Список объектов
      </a>
    </li>
    <li>
      <a href="<?php echo url_for('@backend_product_item_node_new?parent_id=' . $root['id']) ?>">
        <img src="/backend/images/plus_11x11.gif" alt="Добавить объект" title="Добавить объект" border="0" />
        Добавить объект
      </a>
    </li>
    <li>
      <a href="<?php echo url_for('@backend_product_category_new?parent_id=' . $root['id']) ?>">
        <img src="/backend/images/plus_11x11.gif" alt="Добавить категорию" title="Добавить категорию" border="0" />
        Добавить категорию
      </a>
    </li>
    <?php include_component('backendFxShopItem', 'showBackendMenuChildren', array('pid' => $root['id'])); ?>
  </ul>
</div>
<?php endforeach ?>
