<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<h2>Редактирование "<?php echo $param['Translation'][$sf_user->getCulture()]['title'] ?>"</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt>
          <label for="<?php echo $form['value']->renderId() ?>">
            <?php echo $form['value']->renderLabel() ?>:
          </label>
        </dt>
        <dd>
          <?php echo $form['value']->render() ?>
        </dd>
      </dl>

      <dl class="submit">
        <input type="submit" value="Изменить значение" />
      </dl>                    
    </fieldset>
  </form>
</div>