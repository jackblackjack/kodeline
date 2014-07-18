<?php if ($sf_user->hasFlash('notice')): ?>
<?php echo javascript_tag("$.jGrowl('". $sf_user->getFlash('notice') . "', { theme: 'manilla', speed: 'slow', animateOpen: { height: 'show' }, animateClose: { height: 'hide' } });"); ?>
<?php endif ?>

<?php if ($sf_user->hasFlash('error')): ?>
<?php echo javascript_tag("$.jGrowl('". $sf_user->getFlash('error') . "', { theme: 'manilla', speed: 'slow', animateOpen: { height: 'show' }, animateClose: { height: 'hide' } });"); ?>
<?php endif ?>