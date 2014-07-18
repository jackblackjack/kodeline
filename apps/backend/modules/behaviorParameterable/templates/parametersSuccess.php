<?php if (isset($object)): ?>
<h2>
  <?php if ($object['is_category']): ?>
    <a href="<?php echo url_for('@backend_product_item_node?id=' . $object['id']) ?>"><?php echo $object['title'] ?></a>
  <?php else: ?>
    <a href="<?php echo url_for('@backend_product_item_node_edit?id=' . $object['id']) ?>"><?php echo $object['title'] ?></a>
  <?php endif ?>
  : список используемых полей
</h2>
<?php endif ?>

<table id="rounded-corner">
  <thead>
    <tr>
      <th scope="col" class="rounded-company">Название</th>
      <th scope="col" class="rounded-company">Название</th>
      <th scope="col" class="rounded">Тип</th>
      <th scope="col" class="rounded">Обязательное?</th>
      <th scope="col" class="rounded-q4">Действия</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($parametersSchema->count()): ?>
      <?php foreach($parametersSchema as $schema): ?>
        <?php include_partial($sf_context->getModuleName() . '/parametersDefinitionList', array('modelName' => $modelName, 'schema' => $schema)) ?>
      <?php endforeach ?>
    <?php endif ?>
  </tbody>
</table>

<?php if ($objectParamValue): ?>
<a href="<?php echo url_for('@parameterable_component_parameter_new?model=' . $modelName . '&belong=' . $objectParamValue) ?>" class="bt_green">
  <span class="bt_green_lft"></span><strong>Добавить поле</strong><span class="bt_green_r"></span>
</a>
<?php else: ?>
<a href="<?php echo url_for('@parameterable_component_parameter_new?model=' . $modelName) ?>" class="bt_green">
  <span class="bt_green_lft"></span><strong>Добавить поле</strong><span class="bt_green_r"></span>
</a>
<?php endif ?>
