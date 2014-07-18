<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<h2>Новое поле: конфигурация прикрепления (шаг 3)</h2>

<div class="form">
  <?php echo $form->renderFormTag($page->getUrl(array('model' => $modelName)), array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <?php $fields = array_keys($form->getWidgetSchema()->getFields()) ?>
      <?php foreach($fields as $field): ?>
      <?php if ($form[$field]->isHidden()) continue ?>
      <dl>
        <dt><?php echo $form[$field]->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form[$field]->render() ?>
          <span class="hint"><?php echo $form[$field]->renderHelp() ?></span>

          <?php if ($form[$field]->hasError()): ?>
          <span><?php echo $form[$field]->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>
      <?php endforeach ?>
      <dl class="submit">
        <input type="submit" value="Добавить поле" />
      </dl>                    
    </fieldset>
  </form>
</div>