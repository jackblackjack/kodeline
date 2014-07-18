<?php
/**
 */
interface IMixinsCaller
{
    public function __mixin_get_property($property);

    public function __mixin_set_property($property, $value);

    public function __mixin_call($method, $value);
}
