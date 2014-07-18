<?php if (in_array($name, array('id', 'user_id', 'created_at', 'updated_at', 'typeof'))) return false; ?>
<div id="<?php echo $name ?>">
  <label for="<?php echo $name ?>"><?php echo $name ?>:</label>
  <div class="value"><?php echo $value ?></div>
</div>