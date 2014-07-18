<h1>Новый шаблон</h1>

<?php if (! $layouts->count()): ?>
  <h2>Шаблонов для сайтов не создано</h2>
<?php else: ?>
<table>
  <?php foreach($layouts as $layout): ?>
  <tr>
    <td><?php $layout['name'] ?></td>
    <td><?php $layout['annotation'] ?></td>
    <td><?php $layout['width'] ?></td>
    <td><?php $layout['height'] ?></td>
  </tr>
  <?php endforeach ?>
</table>
<?php endif ?>

<a href="<?php echo url_for('@backend_product_category_new') ?>" class="bt_blue">
  <span class="bt_blue_lft"></span><strong>Создать новый шаблон</strong><span class="bt_blue_r"></span>
</a>