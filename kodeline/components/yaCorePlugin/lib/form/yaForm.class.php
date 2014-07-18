<?php

/**
 * Form base class.
 * Extends the form component.
 *
 * @package    yaCorePlugin
 * @subpackage lib.form
 * @author     pinhead
 * @version    SVN: $Id: yaForm.class.php 2341 2010-09-25 20:15:24Z pinhead $
 */
class yaForm extends sfFormSymfony
{
  /**
   */
  protected static $counter = 1;

  protected
    $key,
    $name;

  public function setup()
  {
    parent::setup();

    //$this->widgetSchema->setFormFormatterName('dmList');

    $this->key = 'ya_form_'.self::$counter++;

    $this->setName(yaString::underscore(get_class($this)));
  }

  public function setName($name)
  {
    $this->name = $name;
    $this->widgetSchema->setNameFormat($name.'[%s]');

    return $this;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getKey()
  {
    return $this->key;
  }

  /**
   * Prevent loosing the widgetSchema name format.
   *
   * @see sfForm#setWidgets($widgets)
   */
  public function setWidgets(array $widgets)
  {
    $return = parent::setWidgets($widgets);
    $this->setName($this->name);
    return $return;
  }

  public function removeCsrfProtection()
  {
    $this->localCSRFSecret = false;

    if ($this->isCSRFProtected())
    {
      unset($this[self::$CSRFFieldName]);
    }

    return $this;
  }

  public function changeToHidden($fieldName)
  {
    $this->widgetSchema[$fieldName] = new sfWidgetFormInputHidden;
    return $this;
  }

  public function changeToDisabled($fieldName)
  {
    $this->widgetSchema[$fieldName]->setAttribute('disabled', true);
    return $this;
  }

  public function changeToReadOnly($fieldName)
  {
    $this->widgetSchema[$fieldName]->setAttribute('readonly', true);
    return $this;
  }

  public function changeToEmail($fieldName)
  {
    $this->validatorSchema[$fieldName] = new sfValidatorEmail(
      $this->validatorSchema[$fieldName]->getOptions(),
      $this->validatorSchema[$fieldName]->getMessages()
    );
  }

  /**
   * Binds the current form validate it in one step.
   *
   * @param  array      An array of tainted values to use to bind the form
   * @param  array      An array of uploaded files (in the $_FILES or $_GET format)
   * @param  Connection An optional Doctrine Connection object
   *
   * @return Boolean    true if the form is valid, false otherwise
   */
  public function bindAndValid(sfWebRequest $request)
  {
    return $this->bindRequest($request)->isValid();
  }

  public function bindRequest(sfWebRequest $request)
  {
    $this->bind($request->getParameter($this->name), $request->getFiles($this->name));

    return $this;
  }

  /**
   * Usefull for debugging : will throw the error exception
   */
  public function throwError()
  {
    throw $this->errorSchema;
  }

  protected function disableField($parent_form, $key)
  {
    unset($this->widgetSchema[$parent_form][$key]);
    unset($this->validatorSchema[$parent_form][$key]);
    unset($this->defaults[$parent_form][$key]);
    unset($this->taintedValues[$parent_form][$key]);
    unset($this->values[$parent_form][$key]);
    unset($this->embeddedForms[$parent_form][$key]);
  }

  /**
   * @param array $columns
   * @param sfValidatorBase $validator
   * @return sfValidatorDoctrineUnique
   * <code>
   * public function configure() {
   *  $this->getPostValidatorUnique(array('username'))->setMessage('invalid', 'IN YOUR FACE');
   * }
   * </code>
   */
  public function getPostValidatorUnique($columns, $validator = null)
  {
    if ($validator === null)
    {
      $validator = $this->getValidatorSchema()->getPostValidator();
    }
    if ($validator instanceof sfValidatorDoctrineUnique)
    {
      if (!array_diff($validator->getOption('column'), $columns))
      {
        return $validator;
      }
    }
    elseif (method_exists($validator, 'getValidators'))
    {
      foreach($validator->getValidators() as $childValidator)
      {
        if ($matchingValidator = $this->getPostValidatorUnique($columns, $childValidator))
        {
          return $matchingValidator;
        }
      }
    }
    return null;
  }
}