<?php if (isset($object)): ?>
<h2> верх / "<?php echo $object['title'] ?>" (#<?php echo $object['id'] ?>)</h2>
<?php endif ?>

<table id="rounded-corner">
  <thead>
    <tr>
      <th scope="col" class="rounded-company"></th>
      <th scope="col" class="rounded">Название</th>
      <th scope="col" class="rounded">Значение</th>
      <th scope="col" class="rounded">Действия</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($parametersSchema->count()): ?>
    <?php foreach($parametersSchema as $schema): ?>
    <?php include_partial($sf_context->getModuleName() . '/parametersList', array('modelName' => $modelName, 'schema' => $schema, 'object' => $object)) ?>
    <?php endforeach ?>
    <?php endif ?>
  </tbody>
</table>
