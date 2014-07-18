<?php if (! empty($children) && $children->count()): ?>
  <?php foreach($children as $category): ?>
  <li>
    <a href="<?php echo url_for('@backend_product_item_node?id=' . $category['id']) ?>">
      <img src="/backend/images/open_folder.png" alt="Перейти к просмотру дочерних элементов" title="Перейти к просмотру дочерних элементов" border="0" />
      <?php echo $category['title'] ?>
    </a>
  <li>
  <?php endforeach ?>
<?php endif ?>
