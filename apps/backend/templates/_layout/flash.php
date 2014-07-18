<?php if ($sf_user->hasFlash('warning')): ?>
<div class="warning_box"><?php echo $sf_user->getFlash('warning') ?></div>
<?php endif ?>

<?php if ($sf_user->hasFlash('success')): ?>
<div class="valid_box"><?php echo $sf_user->getFlash('success') ?></div>
<?php endif ?>

<?php if ($sf_user->hasFlash('error')): ?>
<div class="error_box"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>

            