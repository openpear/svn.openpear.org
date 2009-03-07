<?php

function PhobInit()
{
    $PhobMetaclass = new ClassOfPhobMetaclass();
    $classOfPhobObject = $PhobMetaclass->alloc();
    $classOfPhobBehavior = $PhobMetaclass->alloc();
    $classOfPhobClassDescription = $PhobMetaclass->alloc();
    $classOfPhobClass = $PhobMetaclass->alloc();
    $classOfPhobMetaclass = $PhobMetaclass->alloc();

    $PhobClass = new ClassOfPhobClass();
    $PhobObject = new ClassOfPhobObject();
    $PhobObject->initCoreClass('PhobObject', $PhobClass,
                               $classOfPhobObject);
    $PhobBehavior = new ClassOfPhobBehavior();
    $PhobBehavior->initCoreClass('PhobBehavior', $PhobObject,
                                 $classOfPhobBehavior);
    $PhobClassDescription = new ClassOfPhobClassDescription();
    $PhobClassDescription->initCoreClass('PhobClassDescription',
                                         $PhobBehavior,
                                         $classOfPhobClassDescription);
    $PhobClass->initCoreClass('PhobClass', $PhobClassDescription,
                              $classOfPhobClass);
    $PhobMetaclass->initCoreClass('PhobMetaclass', $PhobClassDescription,
                                  $classOfPhobMetaclass);

    $classOfPhobObject->initWithInstanceBehavior($PhobObject, $PhobClass);
    $classOfPhobBehavior->initWithInstanceBehavior($PhobBehavior,
                                                   $classOfPhobObject);
    $classOfPhobClassDescription->initWithInstanceBehavior
        ($PhobClassDescription, $classOfPhobBehavior);
    $classOfPhobClass->initWithInstanceBehavior($PhobClass,
                                                $classOfPhobClassDescription);
    $classOfPhobMetaclass->initWithInstanceBehavior
        ($PhobMetaclass, $classOfPhobClassDescription);

    $classOfPhoblinks = $PhobMetaclass->alloc()
        ->initWithInstanceBehaviorName('Phoblinks', $PhobObject);
    $Phoblinks = $classOfPhoblinks->instanceBehavior();

    Phoblinks::$sharedInstance = $Phoblinks;
    $Phoblinks->addClass($PhobObject);
    $Phoblinks->addClass($PhobBehavior);
    $Phoblinks->addClass($PhobClassDescription);
    $Phoblinks->addClass($PhobClass);
    $Phoblinks->addClass($PhobMetaclass);
    $Phoblinks->addClass($Phoblinks);
}

PhobInit();

