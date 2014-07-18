<?php switch($editor): ?>
<?php case 'wysibb': ?>
  <?php if (isset($iframe) && $iframe): ?>
    <?php $sf_response->setContentType('text/html'); ?>
    <?php sfConfig::set('sf_web_debug', false); ?>
    <?php echo '<html><body>OK<script>window.parent.$("#'. $idarea . '").insertImage("'. $returnData['path'] . $returnData['name'] . '","' . $returnData['path'] . $returnData['name'] . '").closeModal().updateUI();</script></body></html>'; ?>
  <?php else: ?>
    <?php echo json_encode(array(
                  'status'      => 1,
                  'msg'         => 'OK',
                  'image_link'  => sprintf('%s%s', $returnData['path'], $returnData['name']),
                  'thumb_link'  => sprintf('%s%s', $returnData['path'], $returnData['name'])
                )) ?>
  <?php endif ?>
<?php break ?>
<?php default: ?>
  <?php echo json_encode($returnData->getRawValue()); ?>
<?php endswitch ?>
