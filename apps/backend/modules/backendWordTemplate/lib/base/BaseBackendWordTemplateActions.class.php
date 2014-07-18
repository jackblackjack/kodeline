<?php
/**
 * Контроллер работы с шаблонами слов.
 * 
 * @package     backend
 * @subpackage  backendWordTemplate
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseBackendWordTemplateActions extends BaseBackendElementActions
{
  /**
   * Class name for the object
   * @var string
   */
  protected $objectClassName = 'WordTemplate';

  /**
   * Class name of form for create new object
   * @var string
   */
  protected $formClassNew = 'WordTemplateNewForm';

  /**
   * Class name of form for edit object
   * @var string
   */
  protected $formClassEdit = 'WordTemplateNewForm';
}