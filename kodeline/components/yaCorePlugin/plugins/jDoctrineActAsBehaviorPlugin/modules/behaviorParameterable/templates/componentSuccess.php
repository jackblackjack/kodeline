<h2> Параметры модели "<?php echo $modelName ?>"</h2>

<table id="rounded-corner">
  <thead>
    <tr>
      <th scope="col" class="rounded-company"></th>
      <th scope="col" class="rounded">Имя</th>
      <th scope="col" class="rounded">Тип параметра</th>
      <th scope="col" class="rounded">Заголовок</th>
      <th scope="col" class="rounded">Действия</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($parametersSchema->count()): ?>
    <?php foreach($parametersSchema as $schema): ?>
    <?php include_partial(
            $sf_context->getModuleName() . '/parametersDefinitionList', 
            array('modelName' => $modelName, 'schema' => $schema)
          )
    ?>
    <?php endforeach ?>
    <?php endif ?>
  </tbody>
</table>