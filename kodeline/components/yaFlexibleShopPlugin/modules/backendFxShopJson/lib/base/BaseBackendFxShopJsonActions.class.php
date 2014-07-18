<?php
/**
 * Controller of ajax (json) web requests.
 * 
 * @package     backend
 * @subpackage  backendFxShopJson
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseBackendFxShopJsonActions extends jBaseJsonActions
{
  /**
   * Get list of component parameters.
   * 
   * @param sfWebRequest $request
   */
  public function executeGetParameters(sfWebRequest $request)
  {
    // Check for required action dialog.
    if (null === ($iComponentId = $request->getParameter('component_id', null))) {
      throw new sfStopException($this->getContext()->getI18N()->__('Component must be setted!', null, 'flexible-shop'));
    }

    // Fetch value of the type for concrete model.
    $componentName = Doctrine_Core::getTable('jBehaviorComponent')->createQuery()
                      ->select('name')->where('id = ?', $iComponentId)
                      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    // Check component exists.
    if (! $componentName)
    {
      throw new sfException(sprintf($this->getContext()
        ->getI18N()->__('Component "%d" is not found!', null, 'flexible-shop'), $iComponentId));
    }

    // Define result variable.
    $arResult = array();

    // If model has been specified column' value for of Parameterable behavior.
    if (Doctrine::getTable($componentName)->hasTemplate('Parameterable'))
    {
      // Fetch object data by this id.
      $record = Doctrine::getTable($componentName)->getRecordInstance();

      // Fetch schema of the parameters.
      $parameters = $record->fetchExtendedParameters();

      // Prepare parameters.
      foreach($parameters as $parameter) {
        $arResult[] = array('id' => $parameter['id'], 'title' => $parameter['name']);
      }
    }

    // Output result.
    $this->renderJsonResult($arResult);

    // No any templates.
    return sfView::NONE;
  }

  /**
   * Get list of component parameters.
   * 
   * @param sfWebRequest $request
   */
  public function executeGetValueField(sfWebRequest $request)
  {
    // Check for component id.
    if (null === ($iComponent = $request->getParameter('component_id', null))) {
      throw new sfStopException($this->getContext()->getI18N()->__('Parameter "component_id" must be specified!', null, 'flexible-shop'));
    }

    // Check for parameter id.
    if (null === ($iParameter = $request->getParameter('parameter_id', null))) {
      throw new sfStopException($this->getContext()->getI18N()->__('Parameter "parameter_id" must be specified!', null, 'flexible-shop'));
    }

    // Check for form widget name.
    if (null === ($sPrefix = $request->getParameter('prefix', null))) {
      throw new sfStopException($this->getContext()->getI18N()->__('Parameter "prefix" must be specified!', null, 'flexible-shop'));
    }

    // Check for specified parameter "count".
    $iCount = $request->getParameter('count', 1);

    // Fetch schema record about parameter.
    $schema = Doctrine_Core::getTable('jParameterableSchema')->createQuery('ps')
                ->innerJoin('ps.Component')
                ->where('ps.component_id = ?', $iComponent)
                ->andWhere('ps.id = ?', $iParameter)
                ->fetchOne();

    // Check record exists.
    if (! $schema)
    {
      throw new sfException(sprintf($this->getContext()
        ->getI18N()->__('Schema record with ID #%d is not found!', null, 'flexible-shop'), $iSchema));
    }

    // Define classname for parameter's value.
    $parameterValueFormClassName = 'ParameterableParamValue' . sfInflector::camelize($schema['type']) . 'Form';

    $arDefaults = array(
      'component_id'  => $iComponent,
      'parameter_id'  => $iParameter,
      'value'         => $this->object[$this->param['name']],
      'model_name'    => $schema['Component']['name'],
      //'object_id'     => $this->object['id']
    );

    // Define form.
    //$pvForm = new $parameterValueFormClassName($arDefaults, array('length' => $this->param['length']));
    $pvForm = new $parameterValueFormClassName($arDefaults, $arDefaults);

    $arResult = array();
    for($i = 0; $i < $iCount; $i++) {
      $pvForm->setName($sPrefix . '[' . $i . ']');
      $arResult[$i] = $pvForm['value']->render();
    }

    // Output result.
    $this->renderJsonResult($arResult);

    // No any templates.
    return sfView::NONE;
  }
}