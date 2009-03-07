<?php

class ClassOfPhobClassBuilder extends ClassOfPhobObject
{

    private $_propertyBuilderClassByName = array();

    function propertyBuilderClassForName($name)
    {
        if (array_key_exists($name, $this->_propertyBuilderClassByName))
            return $this->_propertyBuilderClassByName[$name];
        else
            return null;
    }

    function setPropertyBuilderClassForName($class, $name)
    {
        $this->_propertyBuilderClassByName[$name] = $class;
    }

    function classBuilderWithClassName($name, $superclass)
    {
        return $this->alloc()->initWithClassName($name, $superclass);
    }

}


class PhobClassBuilder extends PhobObject
{

    protected $_className;
    protected $_instBehaviorClassName;
    protected $_superclass;
    protected $_properties;
    protected $_wrapClassName;
    protected $_wrapInstBehaviorClassName;
    protected $_trait;
    protected $_auxMethsByMeth;

    function initWithClassName($className, $superclass=null)
    {
        $this->_className = $className;
        $this->_instBehaviorClassName = "ClassOf$className";
        if ($superclass)
            $this->_superclass = $superclass;
        else
            $this->_superclass = PhobObject();
        $this->_properties = array();
        $this->_wrapClassName = "__${className}__";
        $this->_wrapInstBehaviorClassName = "__ClassOf${className}__";
        $this->_auxMethsByMeth = array();
        return $this;
    }

    function className()
    {
        return $this->_className;
    }

    function setClassName($name)
    {
        $this->_className = $name;
    }

    function instanceBehaviorClassName()
    {
        return $this->_instBehaviorClassName;
    }

    function setInstanceBehaviorClassName($name)
    {
        $this->_instBehaviorClassName = $name;
    }

    function superclass()
    {
        return $this->_superclass;
    }

    function setSuperclass($class)
    {
        $this->_superclass = $class;
    }

    function wrapperClassName()
    {
        return $this->_wrapClassName;
    }

    function setWrapperClassName($name)
    {
        $this->_wrapClassName = $name;
    }

    function wrapperInstanceBehaviorClassName()
    {
        return $this->_wrapInstBehaviorClassName;
    }

    function setWrapperInstanceBehaviorClassName($name)
    {
        $this->_wrapInstBehaviorClassName = $name;
    }

    function auxiliaryMethodForMethod($modifier, $primary)
    {
        if (array_key_exists($primary, $this->_auxMethsByMeth))
            return $this->_auxMethsByMeth[$primary][$modifier];
    }

    function setAuxiliaryMethodForMethod($meth, $modifier, $primary)
    {
        if (!array_key_exists($primary, $this->_auxMethsByMeth))
            $this->_auxMethsByMeth[$primary] =
                array('before' => null, 'after' => null, 'around' => null);
        $this->_auxMethsByMeth[$primary][$modifier] = $meth;
    }

    function propertyBuilders()
    {
        return $this->_properties;
    }

    function addPropertyBuilder($builder)
    {
        $this->_properties[] = $builder;
    }

    function removePropertyBuilder($builder)
    {
        if (($i = array_search($builder, $htis->_properties)) !== null)
            unset($this->_properties[$i]);
    }

    function trait()
    {
        return $this->_trait;
    }

    function setTrait($trait)
    {
        $this->_trait = $trait;
    }

    function begin()
    {
        if ($this->_trait)
            $this->_trait->beFixed();
    }

    function end()
    {
        if (!class_exists($this->_className))
            Phoblinks()->error("class $this->_className is not defined.");

        $primClass = $this->_superclass->primitiveInstanceBehavior();
        if (get_parent_class($this->_className) !== $primClass)
            Phoblinks()->error("class $this->_className must be " .
                               "a subclass of $primClass.");

        if (!class_exists($this->_instBehaviorClassName))
            $this->createInstanceBehaviorClass();
        $this->createWrapperClasses();
        $this->defineNewClass();
    }

    function createInstanceBehaviorClass()
    {
        $code = "class $this->_instBehaviorClassName extends " .
            get_class($this->_superclass) . '{';
        $code .= '}';
        eval($code);
    }

    function defineNewClass()
    {
        Phoblinks()->defineClass($this->_className, $this->_superclass,
                                 $this->_properties, $this->_wrapClassName,
                                 $this->_wrapInstBehaviorClassName);
    }

    function createWrapperClasses()
    {
        $this->createWrapperClass();
        $this->createWrapperInstanceBehaviorClass();
    }

    protected function createWrapperClass()
    {
        if ($this->_trait)
            $traitMeths = $this->_trait->allMethods();
        else
            $traitMeths = array();

        $code = "class $this->_wrapClassName extends $this->_className {";

        // fields and accessor methods for the properties
        foreach ($this->_properties as $prop) {
            $code .= 'protected $' . $prop->memberName() . ';';
            if ($prop->requiresReader())
                $code .= $prop->valueReaderSourceCode();
            if ($prop->requiresWriter())
                $code .= $prop->valueWriterSourceCode();
        }

        // auxiliary methods
        $refClass = new ReflectionClass($this->_className);
        foreach ($this->_auxMethsByMeth as $primMeth => $auxMeths) {
            if ($refClass->hasMethod($primMeth)) {
                $refMeth = $refClass->getMethod($primMeth);
                if ($refMeth->isAbstract())
                    continue;

                $methCode = '';
                if ($refMeth->isPublic())
                    $methCode .= 'public ';
                else if ($refMeth->isProtected())
                    $methCode .= 'protected ';
                else if ($refMeth->isPrivate())
                    $methCode .= 'private ';
                else if ($refMeth->isPublic())
                    $methCode .= 'public ';
                else if ($refMeth->isFinal())
                    $methCode .= 'final ';

                $methCode .= "function $primMeth(";
                $args = '(';
                $arith = $refMeth->getNumberOfParameters();
                $i = 1;
                foreach ($refMeth->getParameters() as $refParam) {
                    if ($refParamClass = $refParam->getClass())
                        $methCode .= $refParamClass->getName() . ' ';
                    if ($refParam->isPassedByReference())
                        $mehtCode .= '&';

                    $arg = '$' . $refParam->getName();
                    $methCode .= $arg;
                    $args .= $arg;
                    if ($i < $arith)
                        $args .= ',';

                    if ($refParam->isOptional())
                        $methCode .= '=' .
                            var_export($refParam->getDefaultValue(), 1);
                    if ($i < $arith)
                        $methCode .= ',';

                    $i++;
                }
                $args .= ')';
                $methCode .= ')';

                $methBody = '';
                if ($around = $auxMeths['around'])
                    $methBody = "return \$this->$around$args;";
                else {
                    if ($before = $auxMeths['before'])
                        $methBody .= "\$this->$before$args;";


                    $methBody .= '$return = ';
                    if (array_key_exists($primMeth, $traitMeths)) {
                        unset($traitMeths[$primMeth]);
                        $methBody .= sprintf
                            ('%s::%s', $traitMeths[$primMeth], $primMeth);
                    } else
                        $methBody .= "parent::$primMeth";
                    $methBody .= "$args;";

                    if ($after = $auxMeths['after'])
                        $methBody .= "\$this->$after$args;";
                    $methBody .= 'return $return;';
                }
                $methCode .= '{'. $methBody . '}';
                $code .= $methCode;
            }
        }

        // trait's methods
        $traitCode = '';
        foreach ($traitMeths as $methName => $assoc) {
            $className = $assoc[0];
            $realMethName = $assoc[1];
            $traitCode .= "function $methName";
            $reprs = PhobTrait::argumentsRepresentations
                ($className, $realMethName);
            $traitCode .= $reprs[0];
            $traitCode .= " { return $className::$realMethName";
            $traitCode .= $reprs[1];
            $traitCode .= ';}';
        }
        $code .= $traitCode;

        // initialization method
        $code .= 'function init() { parent::init(); ';
        foreach ($this->_properties as $prop) {
            if ($prop->delaysDefaultValueInitialization()) {
                if ($prop->defaultValueInitializer() ||
                    $prop->defaultValue() !== null)
                    $code .= sprintf('$this->%s = PhobProperty::' .
                                     '$defaultValue;',
                                     $prop->memberName());
            } else if ($prop->defaultValueInitializer())
                $code .= sprintf('$this->%s = $this->_class->' .
                                 'propertyNamed(\'%s\')->' .
                                 'defaultValueWithObject($this);',
                                 $prop->memberName(), $prop->name());
            else if (($value = $prop->defaultValue()) !== null)
                $code .= sprintf('$this->%s = $this->_class->' .
                                 'propertyNamed(\'%s\')->defaultValue();',
                                 $prop->memberName(), $prop->name());
        }
        $code .= 'return $this; }}';
        eval($code);
    }

    protected function createWrapperInstanceBehaviorClass()
    {
        $code = "class $this->_wrapInstBehaviorClassName extends " .
            "$this->_instBehaviorClassName {";
        $code .= '}';
        eval($code);
    }

}


class PhobPropertyBuilder extends PhobObject
{

    protected $_name;
    protected $_type;
    protected $_memberName;
    protected $_allowsNull = true;
    protected $_isOptional = true;
    protected $_isTransient = false;
    protected $_isReadOnly = false;
    protected $_defaultValue;
    protected $_defaultValueInitializer;
    protected $_delaysDefault = false;
    protected $_valueTransformerName;
    protected $_valueValidators = array();
    protected $_accessors;
    protected $_tester;

    function initWithName($name)
    {
        parent::init();
        $this->_name = $name;
        $this->_memberName = "_$name";
        $this->_accessors = 'rw';
        return $this;
    }

    function propertyClass()
    {
        return PhobProperty();
    }

    function name()
    {
        return $this->_name;
    }

    function setName($name)
    {
        $this->_name = $name;
    }

    function valueType()
    {
        return $this->_type;
    }

    function setValueType($type)
    {
        $this->_type = $type;
    }

    function memberName()
    {
        return $this->_memberName;
    }

    function setMemberName($name)
    {
        $this->_memberName = $name;
    }

    function allowsNull()
    {
        return $this->_allowsNull;
    }

    function setAllowsNull($flag=true)
    {
        $this->_allowsNull = $flag;
    }

    function isOptional()
    {
        return $this->_isOptional;
    }

    function setOptional($flag=true)
    {
        $this->_isOptional = $flag;
    }

    function isReadOnly()
    {
        return $this->_isReadOnly;
    }

    function setReadOnly($flag=true)
    {
        $this->_isReadOnly = $flag;
    }

    function defaultValue()
    {
        return $this->_defaultValue;
    }

    function setDefaultValue($value)
    {
        $this->_defaultValue = $value;
    }

    function defaultValueInitializer()
    {
        return $this->_defaultValueInitializer;
    }

    function setDefaultValueInitializer($code)
    {
        $this->_defaultValueInitializer = $code;
    }

    function delaysDefaultValueInitialization()
    {
        return $this->_delaysDefault;
    }

    function setDelaysDefaultValueInitialization($flag=true)
    {
        $this->_delaysDefault = $flag;
    }

    function valueTransformerName()
    {
        return $this->_valueTransformerName;
    }

    function setValueTransformerName($name)
    {
        $this->_valueTransformerName = $name;
    }

    function valueValidators()
    {
        return $this->_valueValidators;
    }

    function setValueValidators($validators)
    {
        $this->_valueValidators = $validators;
    }

    function addValueValidator($validator)
    {
        $this->_valueValidators[] = $validator;
    }

    function removeValueValidator($validator)
    {
        if (($i = array_search($validator, $htis->_valueValidators)) !== null)
            unset($this->_valueValidators[$i]);
    }

    function accessors()
    {
        return $this->_accessors;
    }

    function setAccessors($flags)
    {
        $this->_accessors = $flags;
    }

    function requiresReader()
    {
        return strpos($this->_accessors, 'r') !== false;
    }

    function requiresWriter()
    {
        return strpos($this->_accessors, 'w') !== false;
    }

    function valueReaderSourceCode()
    {
        $code = "function $this->_name() {";
        if ($this->_delaysDefault) {
            $code .= sprintf('if ($this->%s === PhobProperty::$defaultValue) {',
                             $this->_memberName);
            if ($this->_defaultValueInitializer)
                $code .= sprintf('$this->%s = $this->klass()->' .
                                 'propertyNamed(\'%s\')->' .
                                 'defaultValueWithObject($this);',
                                 $this->_memberName, $this->_name);
            else if ($this->_defaultValue !== null)
                $code .= sprintf('$this->%s = $this->klass()->propertyNamed' .
                                 '(\'%s\')->defaultValue();',
                                 $this->_memberName, $this->_name);
            $code .= '}';
        }
        $code .= 'return $this->' . $this->_memberName . ';';
        $code .= '}';
        return $code;
    }

    function valueWriterSourceCode()
    {
        $name = PhobKeyValueCodingDefaultImplementation::
            publicSetterMethod($this->_name);
        $code = sprintf('function %s($value) {' .
                        '$prop = $this->klass()->propertyNamed(\'%s\');',
                        $name, $this->_name);
        if ($this->_valueTransformerName) {
            $code .= '$transformer = PhobValueTransformer()->' .
                'valueTransformerForName($prop->valueTransformerName());';
            $code .= '$value = $transformer->transformedValue($value);';
        }
        $code .= "\$this->$this->_memberName = \$prop->validateValue(\$value); }";
        return $code;
    }

}


class PhobBuilderCascade extends PhobObject
{

    protected $_builder;
    protected $_ref;
    
    function initWithBuilder($builder)
    {
        parent::init();
        $this->_builder = $builder;
        $this->_ref = new ReflectionClass(get_class($builder));
        return $this;
    }

    function builder()
    {
        return $this->_builder;
    }

    function invokeSetter($name, $args)
    {
        if ($this->_ref->hasMethod('set' . ucfirst($name))) {
            call_user_func_array(array($this->_builder, 'set' . ucfirst($name)),
                                 $args);
            return true;
        } else if ($this->_ref->hasMethod('add' . ucfirst($name))) {
            call_user_func_array(array($this->_builder, 'add' . ucfirst($name)),
                                 $args);
            return true;
        } else if (substr($name, 0, 2) == 'is') {
            $setter = 'set' . ucfirst(substr($name, 2));
            if ($this->_ref->hasMethod($setter)) {
                call_user_func_array(array($this->_builder, $setter), $args);
                return true;
            } else
                return null;
       } else
            return null;
    }

    function __call($name, $args)
    {
        if ($this->invokeSetter($name, $args))
            return $this;
        else
            parent::__call($name, $args);
    }

}


class PhobClassBuilderCascade extends PhobBuilderCascade
{

    protected $_properties;

    function initWithClassName($name, $superclass, $builderClass=null)
    {
        if (!$builderClass)
            $builderClass = PhobClassBuilder();
        $builder = $builderClass
            ->classBuilderWithClassName($name, $superclass);
        $this->initWithBuilder($builder);
        $this->_properties = array();
        return $this;
    }

    function property($name, $builderName=null)
    {
        $property = PhobPropertyBuilderCascade()->alloc()
            ->initWithName($name, $builderName);
        $this->_properties[] = $property;
        return $property;
    }

    function begin()
    {
        foreach ($this->_properties as $prop)
            $this->_builder->addPropertyBuilder($prop->builder());
        $this->_builder->begin();
        return $this;
    }

    function end()
    {
        $this->_builder->end();
        return $this;
    }

}


class PhobPropertyBuilderCascade extends PhobBuilderCascade
{

    protected $_builder;

    function initWithName($name, $builderName=null)
    {
        if ($builderName) {
            $builderClass = PhobClassBuilder()
                ->propertyBuilderClassForName($builderName);
            if (!$builderClass)
                throw new InvalidArgumentException
                    ("No such property builder `$builderName'.");
        } else
            $builderClass = PhobPropertyBuilder();
        $builder = $builderClass->alloc()->initWithName($name);
        return $this->initWithBuilder($builder);
    }

}

Phoblinks()->defineClass('PhobClassBuilder');
Phoblinks()->defineClass('PhobPropertyBuilder');
Phoblinks()->defineClass('PhobBuilderCascade');
Phoblinks()->defineClass('PhobClassBuilderCascade');
Phoblinks()->defineClass('PhobPropertyBuilderCascade');

