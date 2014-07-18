<?php
/**
 */
abstract class Mixin
{
    /** @var IMixinsCaller $parent_object */
    private $parent_object;

    public function __construct(IMixinsCaller $parent_object)
    {
        $this->parent_object = $parent_object;
    }

    public function __get($property)
    {
        return $this->parent_object->__mixin_get_property($property);
    }

    public function __set($property, $value)
    {
        return $this->parent_object->__mixin_set_property($property, $value);
    }

    public function __call($method, $value)
    {
        return $this->parent_object->__mixin_call($method, $value);
    }
}