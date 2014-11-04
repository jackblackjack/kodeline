<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<div class="mws-panel grid_4">
  <div class="mws-panel-header">
    <span>Новый элемент: Свойства</span>
  </div>

  <div class="mws-panel-body no-padding">
    <?php echo $form->renderFormTag(null, array('method' => 'POST', 'class' => 'mws-form')) ?>
    <?php echo $form->renderHiddenFields() ?>
    <fieldset class="mws-form-inline">
      <legend>Основные параметры</legend>

      <div class="mws-form-row bordered">
        <label class="mws-form-label"><?php echo $form['title']->renderLabel() ?></label>
        <div class="mws-form-item">
          <?php echo $form['title']->render() ?>
          <div class="mws-hint"><?php echo $form['title']->renderHelp() ?></div>
          
          <?php if ($form['title']->hasError()): ?>
          <div class="mws-error"><?php echo $form['title']->renderError() ?></div>
          <?php endif ?>
        </div>
      </div>

      <div class="mws-form-row bordered">
        <label class="mws-form-label"><?php echo $form['annotation']->renderLabel() ?></label>
        <div class="mws-form-item">
          <?php echo $form['annotation']->render() ?>
          <div class="mws-hint"><?php echo $form['annotation']->renderHelp() ?></div>
          
          <?php if ($form['annotation']->hasError()): ?>
          <div class="mws-error"><?php echo $form['annotation']->renderError() ?></div>
          <?php endif ?>
        </div>
      </div>

      <div class="mws-form-row bordered">
        <label class="mws-form-label"><?php echo $form['detail']->renderLabel() ?></label>
        <div class="mws-form-item">
          <?php echo $form['detail']->render() ?>
          <div class="mws-hint"><?php echo $form['detail']->renderHelp() ?></div>
          
          <?php if ($form['detail']->hasError()): ?>
          <div class="mws-error"><?php echo $form['detail']->renderError() ?></div>
          <?php endif ?>
        </div>
      </div>
    </fieldset>

    <fieldset class="mws-form-inline">
      <legend>Расширенные параметры</legend>

      <?php if ($paramsForm = $form->getEmbeddedForm('parameters')): ?>
        <?php echo $form['parameters']->renderHiddenFields() ?>
        
        <?php $arParamForms = $paramsForm->getEmbeddedForms(); ?>
        <?php foreach ($arParamForms as $pname => $pform): ?>
          <?php echo $form['parameters'][$pname]->renderHiddenFields() ?>
          <div class="mws-form-row bordered">
            <?php $translation = $pform->getOption('Translation'); ?>
            <label class="mws-form-label" for="<?php echo $form['parameters'][$pname]['value']->renderId() ?>"><?php echo $translation['ru']['title'] ?></label>
            <div class="mws-form-item">
              <?php echo $form['parameters'][$pname]['value']->render() ?>
              <div class="mws-hint">
                <?php echo $translation['ru']['hint'] ?>
                <?php echo $form['parameters'][$pname]['value']->renderHelp() ?>
              </div>

              <?php if ($form['parameters'][$pname]['value']->hasError()): ?>
              <div class="mws-error"><?php echo $form['parameters'][$pname]['value']->renderError() ?></div>
              <?php endif ?>
            </div>
          </div>
        <?php endforeach ?>
      <?php endif ?>
    </fieldset>

    <div class="mws-button-row">
      <input type="submit" value="Создать" class="btn btn-danger">
      <input type="reset" value="Сбросить" class="btn ">
    </div>
  </form>
  </div>      
</div>

