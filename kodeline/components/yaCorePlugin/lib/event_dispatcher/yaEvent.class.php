<?php
/**
 * sfEvent.
 *
 * @package    symfony
 * @subpackage event_dispatcher
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfEvent.class.php 8698 2008-04-30 16:35:28Z fabien $
 */
class yaEvent extends sfEvent implements Serializable
{
  /**
   */
  public function serialize()
  {
    return serialize($this->data);
  }

  /**
   */
  public function unserialize($data)
  {
    $this->data = unserialize($data);
  }
}