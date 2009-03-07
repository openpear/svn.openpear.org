<?php

interface PhobValidation
{

    function validateValueForKey($value, $key);

}


class PhobValidationException extends Exception
{
}

