<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<h2>Новый объект</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt><?php echo $form['title']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['title']->render() ?>
          <span class="hint"><?php echo $form['title']->renderHelp() ?></span>

          <?php if ($form['title']->hasError()): ?>
          <span><?php echo $form['title']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['annotation']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['annotation']->render() ?>
          <span class="hint"><?php echo $form['annotation']->renderHelp() ?></span>

          <?php if ($form['annotation']->hasError()): ?>
          <span><?php echo $form['annotation']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['detail']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['detail']->render() ?>
          <span class="hint"><?php echo $form['detail']->renderHelp() ?></span>

          <?php if ($form['detail']->hasError()): ?>
          <span><?php echo $form['detail']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <?php if ($paramsForm = $form->getEmbeddedForm('parameters')): ?>
        <?php echo $form['parameters']->renderHiddenFields() ?>
        
        <?php $arParamForms = $paramsForm->getEmbeddedForms(); ?>
        <?php foreach ($arParamForms as $pname => $pform): ?>
          <?php echo $form['parameters'][$pname]->renderHiddenFields() ?>
        <dl>
          <dt>
            <label for="<?php echo $form['parameters'][$pname]['value']->renderId() ?>">
              <?php echo $pform->getOption('Translation')['ru']['title'] ?>
            </label>:
          </dt>
          <dd>
            <?php echo $form['parameters'][$pname]['value']->render() ?>
            <span class="hint"><?php echo $pform->getOption('Translation')['ru']['hint'] ?></span>
          </dd>
        </dl>
        <?php endforeach ?>
      <?php endif ?>

      <dl class="submit">
        <input type="submit" value="Добавить" />
      </dl>                    
    </fieldset>
  </form>
</div>


