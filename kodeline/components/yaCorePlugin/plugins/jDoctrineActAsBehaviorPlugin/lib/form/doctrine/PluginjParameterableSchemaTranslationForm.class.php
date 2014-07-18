<?php
/**
 * PluginjParameterableSchemaTranslation form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginjParameterableSchemaTranslationForm extends BasejParameterableSchemaTranslationForm
{
  /**
   * {@inheritdoc}
   */
  public function setup()
  {
    parent::setup();

    // Redefine title field.
    $this->setWidget('title', new sfWidgetFormInputText());
    $this->setValidator('title', new sfValidatorString(array('required' => false)));

    // Set widgets labels.
    $this->getWidgetSchema()->setLabels(array(
      'title'           => 'Имя поля',
      'hint'            => 'Подсказка'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'title'       => 'Наименование поля при выводе на сайте',
      'hint'        => 'Пояснение о назначении и цели заполнения поля'
    ));
  }
}
