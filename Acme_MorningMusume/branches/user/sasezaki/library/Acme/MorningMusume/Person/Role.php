<?php

class Acme_MorningMusume_Person_Role
{
    protected $_roleId = '';

    public function __current($role)
    {
        $this->_roleId = $role;
    }


}
