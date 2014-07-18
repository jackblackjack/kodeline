<h2>Поля для модели <?php echo $modelName ?></h2>

<table id="rounded-corner">
  <thead>
    <tr>
      <th scope="col" class="rounded-company">Имя</th>
      <th scope="col" class="rounded">Тип параметра</th>
      <th scope="col" class="rounded">Заголовок</th>
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

<a href="<?php echo url_for('@parameterable_component_parameter_new?model=' . $modelName . '&belong=' . $sf_request->getParameter('belong')) ?>" class="bt_green">
  <span class="bt_green_lft"></span><strong>Добавить поле</strong><span class="bt_green_r"></span>
</a>
