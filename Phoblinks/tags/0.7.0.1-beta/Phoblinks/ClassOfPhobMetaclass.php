<?php

class ClassOfPhobMetaclass extends ClassOfPhobClassDescription
{

    function initCoreClass($name, $superclass, $metaclass)
    {
        parent::initCoreClass($name, $superclass, $metaclass);
        $metaclass->_class = $this;
        return $this;
    }

    function alloc()
    {
        return new PhobMetaclass($this);
    }

}

