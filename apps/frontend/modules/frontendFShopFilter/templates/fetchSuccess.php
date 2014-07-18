<h2>Результат выборки фильтра:</h2>
<?php if (! $results->count()): ?>
  <h4>Ничего не выбрано</h4>
<?php else: ?>
  <ul>
  <?php foreach($results as $result): ?>
    <li><?php echo $result['title'] ?></li>
  <?php endforeach ?>
  </ul>
<?php endif ?>
<a href="<?php echo url_for('@homepage') ?>">на главную</a>