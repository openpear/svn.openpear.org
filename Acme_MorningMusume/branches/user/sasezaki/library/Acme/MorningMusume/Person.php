<?php


class Acme_MorningMusume_Person
{
    protected $_roles = array();

    public function __construct()
    {
    }

    public function addRole(Acme_MorningMusume_Person_Role $role)
    {
        $this->_roles[] = $role;
    }
}
