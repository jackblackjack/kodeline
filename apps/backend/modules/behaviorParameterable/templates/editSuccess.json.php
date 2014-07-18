<?php $stylesheets = $form->getEmbeddedForm('options')->getStylesheets() ?>
<?php $javascripts = $form->getEmbeddedForm('options')->getJavascripts() ?>
<?php echo json_encode(array('t' => $type, 'j' => $javascripts, 's' => $stylesheets, 'f' => get_partial($sf_context->getModuleName() . '/form' . ucfirst(sfInflector::camelize($type)) . 'Options', array('form' => $form, 'type' => $type, 'culture' => $culture)))) ?>
