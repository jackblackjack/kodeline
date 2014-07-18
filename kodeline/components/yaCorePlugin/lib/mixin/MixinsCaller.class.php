<?php
/**
 */
abstract class MixinsCaller implements IMixinsCaller
{
    protected $mixins = array();

    public function __call($name, $arguments)
    {
        if (!empty($this->mixins))
        {
            foreach ($this->mixins as $mixin)
            {
                if (method_exists($mixin, $name))
                {
                    return call_user_func_array(array($mixin, $name), $arguments);
                }
            }
        }
        trigger_error('Non-existent method was called in class '.__CLASS__.': '.$name, E_USER_WARNING);
    }

    public function __mixin_get_property($property)
    {
        if (property_exists($this, $property))
        {
            return $this->$property;
        }
        trigger_error('Non-existent property was get in class '.__CLASS__.': '.$property, E_USER_WARNING);
    }

    public function __mixin_set_property($property, $value)
    {
        if (property_exists($this, $property))
        {
            return $this->$property = $value;
        }
        trigger_error('Non-existent property was set in class '.__CLASS__.': '.$property, E_USER_WARNING);
    }

    public function __mixin_call($method, $value)
    {
        if (method_exists($this, $method))
        {
            return call_user_func_array(array($this, $method), $value);
        }
        trigger_error('Non-existent method was called in class '.__CLASS__.': '.$method, E_USER_WARNING);
    }

    public function AddMixin($mixin)
    {
        $this->mixins[] = $mixin;
    }
}