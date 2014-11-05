<?php
/**
 * Форма добавления/редактирования 
 * значений расширенных полей объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class FxShopParameterableValuesForm extends yaForm
{
  /**
   * Identification for form.
   * @var string
   */
  const FORM_IDENT = 'parameters';

  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Call parent form method before.
    parent::configure();

    // 
    if (null === $this->getOption('component', null)) {
      throw new sfException(sfContext::getInstance()->getI18N()->__('Наименование компонента не указано!', null, 'flexible-tree'));
    }

    //
    if (null === $this->getOption('object_id', null)) {
      throw new sfException(sfContext::getInstance()->getI18N()->__('Уникальный номер элемента не найден!', null, 'flexible-tree'));
    }

    // Fetch object of the component.
    $object = Doctrine_Core::getTable($this->getOption('component'))
                ->createQuery()
                ->andWhere('id = ?', $this->getOption('object_id'))
                ->fetchOne();

    // Check object if exists.
    if (! $object) {
      throw new sfException(sprintf(sfContext::getInstance()
          ->getI18N()->__('Объект с ID %d не найден!', null, 'flexible-tree'), $this->getOption('object_id')));
    }

    // Fetch schema of the parameters.
    $parametersSchema = $object->fetchExtendedParametersSchema();

    // Build embeded forms for each parameter.
    foreach($parametersSchema as $parameter) {
      //echo '<pre>'; var_dump($parameter->toArray()); die;

      // Define form class name for parameter.
      $parameterFormClassName = 'ParameterableParamValue' . sfInflector::camelize($parameter['type']) . 'Form';

      //@todo: добавить обработку require.
      $parameterFormClassName = new $parameterFormClassName(
        array(
          'value'         => ($this->getOption('set_values', false) ? $object->fetchExtendedParameterValue($parameter['id'], $parameter['type']) : null),
          'parameter_id'  => $parameter['id'],
          'component'     => $this->getOption('component'),
          'object_id'     => $this->getOption('object_id')
        ),
        $parameter->toArray()
      );

      //$parameterFormClassName->getWidgetSchema()->addFormFormatter('embeddedForm', new sfWidgetFormSchemaFormatterBehaviorParameterableEmbedForm($parameterFormClassName->getWidgetSchema()));
      //$parameterFormClassName->getWidgetSchema()->setFormFormatterName('embeddedForm');

      // Embed current form by parameterable form value.
      $this->embedForm($parameter['name'], $parameterFormClassName);
    }
  }

  /**
   * 
   */
  public function saveFormValues($con, sfFormObject $objectForm)
  {
    // Fetch form values as array.
    $arValues = $objectForm->getValues();

    // Check parameters has been sent by form.
    if (! array_key_exists(self::FORM_IDENT, $arValues)) {
      return false;
    }

    $arFormsValues = array();
    foreach ($this->embeddedForms as $fname => $form)
    {
      if (! $form->isBound())
      {
        // Bind values for form.
        $form->bind($arValues[self::FORM_IDENT][$fname]);
      }

      if ($form->isValid()) {

        $formValues = $form->getValues();

        // Throw exception if array values has not exists required parameters.
        if (empty($formValues['parameter_id'])) {
          throw new sfException(sprintf(sfContext::getInstance()
            ->getI18N()->__('Параметер не указан для поля "%s"!', null, 'flexible-tree'), $fname));
        }

        die('ok!');
        $arFormsValues[$objectForm->getObject()->getId()][$formValues['parameter_id']] = $formValues['value'];

        var_dump($arFormsValues); die;
        //$arFormsValues[$this->getOption('component')][$objectForm->getObject()->getId()][$fname] = $form->getValues();
      }
    }

    // If count of values is null - return false.
    if (! count($arFormsValues)) {
      return false;
    }

    // Save values for object.
    return ParameterableToolkit::setExtendedParametersValues($this->getOption('component'), $arFormsValues);
    
    /*
    $updateQuery = Doctrine_Core::getTable($this->getOption('component'))->createQuery()->update();

    foreach ($arFormsValues as $field => $values)
    {
      $updateQuery->set($field, '?', $values['value']);
    }

    $updateQuery->andWhere('id = ?', $objectForm->getObject()->getId())->execute();
    */
  }
}