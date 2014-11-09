<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<div class="grid_8">
  <div class="mws-panel">
    <div class="mws-panel-header">
      <span><i class="icon-loading-2"></i> Новый фильтр</span>
    </div>

    <div class="mws-panel-body no-padding">
      <?php echo $form->renderFormTag(null, array('method' => 'POST', "class" => "mws-form")) ?>         
        <?php echo $form->renderHiddenFields() ?>

        <fieldset class="mws-form-inline">
          <legend>Свойства фильтра</legend>

          <div class="grid_2">
            <div class="mws-form-row">
              <label class="mws-form-label" for="<?php echo $form['is_active']->renderId() ?>">
                <?php echo $form['is_active']->renderLabel() ?>:
              </label>

              <div class="mws-form-item">
                <ul class="mws-form-list inline">
                  <li>
                    <div class="ibutton-container" style="width: 53px;">
                      <?php echo $form['is_active']->render(array("class" => "large")) ?>
                    </div>
                  </li>
                </ul>
              </div>

              <?php if ($form['is_active']->hasError()): ?>
              <span><?php echo $form['is_active']->renderError() ?></span>
              <?php endif ?>
            </div>
          </div>
          
          <div class="grid_6">
            <div class="mws-form-row">
              <label for="<?php echo $form['title']->renderId() ?>" class="mws-form-label">
                <?php echo $form['title']->renderLabel() ?>:
              </label>
              <div class="mws-form-item">
                <?php echo $form['title']->render(array("class" => "large", "placeholder" => "Название фильтра")) ?>

                <?php if ($form['title']->hasError()): ?>
                <span><?php echo $form['title']->renderError() ?></span>
                <?php endif ?>
              </div>
            </div>
          </div>

          <div class="grid_4 mws-form-block">
            <div class="mws-form-row">
              <label for="<?php echo $form['annotation']->renderId() ?>" class="mws-form-label">
                <?php echo $form['annotation']->renderLabel() ?>:
              </label>
              <div class="mws-form-item">
                <?php echo $form['annotation']->render(array("class" => "large autosize", "placeholder" => "Краткое описание фильтра")) ?>
                <?php if ($form['annotation']->hasError()): ?>
                <span><?php echo $form['annotation']->renderError() ?></span>
                <?php endif ?>
              </div>
            </div>
          </div>

          <div class="grid_4 mws-form-block">
            <div class="mws-form-row">
              <label for="<?php echo $form['detail']->renderId() ?>" class="mws-form-label">
                <?php echo $form['detail']->renderLabel() ?>:
              </label>
              <div class="mws-form-item">
                <?php echo $form['detail']->render(array("class" => "large", "placeholder" => "Расширенное описание фильтра")) ?>

                <?php if ($form['detail']->hasError()): ?>
                <span><?php echo $form['detail']->renderError() ?></span>
                <?php endif ?>
              </div>
            </div>
          </div>
        </fieldset>

        <fieldset class="mws-form-inline">
          <legend>Правила фильтрации</legend>

          <?php $szRules = count($form->getEmbeddedForm('rules')->getEmbeddedForms()) ?>
          <?php for ($i = 0; $i < $szRules; $i++): ?>
            <?php include_partial('parameterForm', array('rule_ix' => $i, 'form' => $form)) ?>
          <?php endfor ?>
        </fieldset>

        <div class="mws-button-row">
          <input type="submit" class="btn btn-danger" value="Создать фильтр" />
        </div>
      </div>
    </form>
  </div>
</div>  
                                                                                                                                                                                                                                              