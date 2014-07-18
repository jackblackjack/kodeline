<?php
/**
 */
class WordTemplateNewForm extends WordTemplateForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Define widget goods_id
    $this->setWidget('goods_id', new jWidgetFormDoctrineFBSuggest(
      array(
        'model'           => 'Goods',
        'name'            => 'goods',
        'maxshownitems'   => 20,
        'maxitems'        => 1,
        'cache'           => false,
        'input_min_size'  => 2,
        'add_empty'       => false,
        'multiple'        => false,
        'async'           => true
      )
    ));

    $this->validatorSchema['goods_id']   = new sfValidatorCallback(array('callback' => array($this, 'goodsCallback')));
  }

  public function goodsCallback($validator, $value)
  {
    return array_shift($value);
  }
}
