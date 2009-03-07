<?php

require_once dirname(__FILE__) . '/../src/Phoblinks.php';

//
// Defining a trait
//

class Inspecting
{

    static function inspect($self)
    {
        $inspect = array();
        $refClass = new ReflectionClass($self);
        foreach ($refClass->getProperties() as $refProperty) {
            if ($refProperty->isPublic()) {
                $name = $refProperty->getName();
                $inspect[$name] = $self->$name;
            }
        }
        return $inspect;
    }

}

Phoblinks()->defineTrait('Inspecting');


//
// Defining a new class using the trait
//

$cls = Phoblinks()->toDefineClass('NewObject');
$cls->trait(Inspecting())
    ->begin();

class NewObject extends PhobObject
{

    public $_name;

    function initWithName($name)
    {
        $this->init();
        $this->_name = $name;
        return $this;
    }

    function name()
    {
        return $this->_name;
    }

}

$cls->end();


$obj = NewObject()->alloc()->initWithName('John Doe');
print 'name = ' . $obj->name() . "\n";
var_dump($obj->inspect());
print "\n";


//
// Trait composition
//

class Descripting
{

    static function description($self)
    {
        $s = '<' . $self->klass()->name();
        foreach ($self->inspect() as $key => $value)
            $s .= " $key=" . var_export($value, true);
        $s .= '>';
        return $s;
    }

}

Phoblinks()->defineTrait('Descripting');

$cls = Phoblinks()->toDefineClass('NewObject2', NewObject());
$newTrait = Inspecting()->traitByUnioningTrait(Descripting());
$cls->trait($newTrait)
    ->begin();

class NewObject2 extends __NewObject__
{
}

$cls->end();

$obj = NewObject2()->alloc()->initWithName('John Doe 2');
print "description = " . $obj->description() . "\n";

