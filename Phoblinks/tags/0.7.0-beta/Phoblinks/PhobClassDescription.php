<?php

class PhobClassDescription extends PhobBehavior
{

    protected $_properties;

    function init()
    {
        parent::init();
        $this->_properties = array();
        return $this;
    }

    function classBehavior()
    {
        return $this->klass();
    }

    function allProperties()
    {
        if ($this->_superclass) {
            $props = $this->_superclass->allProperties();
            return array_merge($props, $this->_properties);
        } else
            return $this->_properties;
    }

    function properties()
    {
        return $this->_properties;
    }

    function propertyNamed($name)
    {
        if (array_key_exists($name, $this->_properties))
            return $this->_properties[$name];
        else if ($this->_superclass)
            return $this->_superclass->propertyNamed($name);
        else
            return null;
    }

    protected function addProperty($prop)
    {
        $this->_properties[$prop->name()] = $prop;
    }

}

