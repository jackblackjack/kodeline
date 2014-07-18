<h2>Редактирование фильтра "<?php echo $object['title'] ?>"</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt><label for="<?php echo $form['is_active']->renderId() ?>"><?php echo $form['is_active']->renderLabel() ?>:</label></dt>
        <dd><?php echo $form['is_active']->render() ?></dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['title']->renderId() ?>"><?php echo $form['title']->renderLabel() ?>:</label></dt>
        <dd><?php echo $form['title']->render() ?></dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['annotation']->renderId() ?>"><?php echo $form['annotation']->renderLabel() ?>:</label></dt>
        <dd><?php echo $form['annotation']->render() ?></dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['detail']->renderId() ?>"><?php echo $form['detail']->renderLabel() ?>:</label></dt>
        <dd><?php echo $form['detail']->render() ?></dd>
      </dl>

      <?php $szRules = count($form->getEmbeddedForm('rules')->getEmbeddedForms()) ?>
      <?php for ($i = 0; $i < $szRules; $i++): ?>
        <?php include_partial('backendFShopFilter/ruleForm', array('rule' => $i, 'form' => $form)) ?>
      <?php endfor ?>

      <dl class="submit"><input type="submit" value="Сохранить изменения" /></dl>                    
    </fieldset>
  </form>
</div>