<?php

class Acme_MorningMusume
{

    const MODE_STRICT = 0;
    const MODE_LAZY   = 1;

    public function __construct($mode)
    {
    }

    public function getPerson($name)
    {
        if ($this->_mode == self::MODE_STRICT)
        {
            if ($name == 'nacchi')
            {
                return false;
            }
        }

        $people = new Acme_MorningMusume_Person();
        $people->addRole();
        $people->addRole('UnkoSuruyo');

        return $person;

    }

    public function getGroup($groupName)
    {


        if (array_key_exists($groupName, $groups)) 
        {

        }
    }
}
