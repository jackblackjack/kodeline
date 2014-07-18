<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php include_partial('global/block/metas', array('metas' => array('title' => "Редактирование пользователя"))); ?>

<h2>Редактирование пользователя</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt><?php echo $form['is_active']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['is_active']->render() ?>
          <span class="hint"><?php echo $form['is_active']->renderHelp() ?></span>

          <?php if ($form['is_active']->hasError()): ?>
          <span><?php echo $form['is_active']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['is_super_admin']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['is_super_admin']->render() ?>
          <span class="hint"><?php echo $form['is_super_admin']->renderHelp() ?></span>

          <?php if ($form['is_super_admin']->hasError()): ?>
          <span><?php echo $form['is_super_admin']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['first_name']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['first_name']->render() ?>
          <span class="hint"><?php echo $form['first_name']->renderHelp() ?></span>

          <?php if ($form['first_name']->hasError()): ?>
          <span><?php echo $form['first_name']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['last_name']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['last_name']->render() ?>
          <span class="hint"><?php echo $form['last_name']->renderHelp() ?></span>

          <?php if ($form['last_name']->hasError()): ?>
          <span><?php echo $form['last_name']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['email_address']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['email_address']->render() ?>
          <span class="hint"><?php echo $form['email_address']->renderHelp() ?></span>

          <?php if ($form['email_address']->hasError()): ?>
          <span><?php echo $form['email_address']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['username']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['username']->render() ?>
          <span class="hint"><?php echo $form['username']->renderHelp() ?></span>

          <?php if ($form['username']->hasError()): ?>
          <span><?php echo $form['username']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['password']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['password']->render() ?>
          <span class="hint"><?php echo $form['password']->renderHelp() ?></span>

          <?php if ($form['password']->hasError()): ?>
          <span><?php echo $form['password']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['groups_list']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['groups_list']->render() ?>
          <span class="hint"><?php echo $form['groups_list']->renderHelp() ?></span>

          <?php if ($form['groups_list']->hasError()): ?>
          <span><?php echo $form['groups_list']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['permissions_list']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['permissions_list']->render() ?>
          <span class="hint"><?php echo $form['permissions_list']->renderHelp() ?></span>

          <?php if ($form['permissions_list']->hasError()): ?>
          <span><?php echo $form['permissions_list']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <?php if ($profileForm = $form->getEmbeddedForm('profile')): ?>
        <?php foreach ($form['profile'] as $name => $field): ?>
        <?php if ($field->isHidden()) continue; ?>
        <dl>
          <dt><?php echo $form['profile'][$name]->renderLabel() ?>:</dt>
          <dd>
            <?php echo $form['profile'][$name]->render() ?>
            <span class="hint"><?php echo $form['profile'][$name]->renderHelp() ?></span>

            <?php if ($form['profile'][$name]->hasError()): ?>
            <span><?php echo $form['profile'][$name]->renderError() ?></span>
            <?php endif ?>
          </dd>
        </dl>
        <?php endforeach ?>
      <?php endif ?>

      <dl class="submit">
        <input type="submit" value="Сохранить изменения" />
      </dl>                    
    </fieldset>
  </form>
</div>
