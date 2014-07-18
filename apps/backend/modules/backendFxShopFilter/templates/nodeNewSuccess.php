<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<h2>Создание нового фильтра</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt><label for="<?php echo $form['is_active']->renderId() ?>"><?php echo $form['is_active']->renderLabel() ?>:</label></dt>
        <dd><?php echo $form['is_active']->render() ?></dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['parent_id']->renderId() ?>"><?php echo $form['parent_id']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['parent_id']->render() ?>

          <?php if ($form['parent_id']->hasError()): ?>
          <span><?php echo $form['parent_id']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['title']->renderId() ?>"><?php echo $form['title']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['title']->render() ?>

          <?php if ($form['title']->hasError()): ?>
          <span><?php echo $form['title']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['annotation']->renderId() ?>"><?php echo $form['annotation']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['annotation']->render() ?>

          <?php if ($form['annotation']->hasError()): ?>
          <span><?php echo $form['annotation']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['detail']->renderId() ?>"><?php echo $form['detail']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['detail']->render() ?>

          <?php if ($form['detail']->hasError()): ?>
          <span><?php echo $form['detail']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <h2>Выберите условия фильтрации</h2>
      <table id="rounded-corner">
        <tbody>
          <tr>
            <td>
              <?php $szParameters = count($form->getEmbeddedForm('rules')->getEmbeddedForms()) ?>
              <?php for ($i = 0; $i < $szParameters; $i++): ?>
                <?php include_partial('parameterForm', array('parameter' => $i, 'form' => $form)) ?>
              <?php endfor ?>
            </tr>
          </tr>
        </tbody>
      </table>

      <dl class="submit">
        <input type="submit" value="Добавить фильтр" />
      </dl>                    
    </fieldset>
  </form>
</div>