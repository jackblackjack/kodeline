<?php
/**
 * Base class for parameterable steps.
 */
class BaseParameterableStepsPage extends yaMultipageBase
{
  /**
   * List of the steps.
   * @var string
   */
  const STEP1 = 'step1';
  const STEP2 = 'step2';
  const STEP3 = 'step3';
  const STEP4 = 'last';

  /**
   * Name of storage for save steps values.
   * @var string
   */
  const ORDER_HOLDER_NAME = __CLASS__;
}
