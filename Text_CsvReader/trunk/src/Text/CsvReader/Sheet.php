<?php
class Text_CsvReader_Sheet extends  Text_CsvReader_Base
{
  protected
    $iterator = null,
    $writers = array(),
    $requiredOptions = array('reader'),
    $options = array(
                     'depends' => array(),
                     'prefilter' => array(),
                     'validator' => array(),
                     'postfilter' => array(),
                     'writer' => array(),
                     ),
    $validatorException = null;

  public function __construct($options = array(), $messages = array())
  {
    parent::__construct($options, $messages);
    $iterator = $this->prepareReaderIterator();
    $writers = $this->prepareInstances('writer');
    if (!$iterator || !$writers) {
      throw new CsvReaderException('no iterator or writer.');
    }
    $this->iterator = $iterator;
    $this->writers = $writers;
  }
  protected function prepareReaderIterator()
  {
    $reader_iterators = $this->prepareInstances('reader');
    if (sizeof($reader_iterators) == 0) {
      throw new CsvReaderException('no reader specified.');
    } elseif (sizeof($reader_iterators) == 1) {
      $iterator = $reader_iterators[0];
    } else {
      throw new CsvReaderException('TODO: append filters.');
    }
    $iterator = $this->getFilterInstance($iterator, $this->getOption('prefilter'));
    $validators = $this->prepareInstances('validator');
    if ($validators) {
      $iterator = $this->getFilterInstance($iterator,
                                           array('ValidatorManager' =>
                                                 array('validators' => $validators)));
    }
    $iterator = $this->getFilterInstance($iterator, $this->getOption('postfilter'));
    return $iterator;
  }
  protected function getFilterInstance(Iterator $inner_iterator, $filter_iterators)
  {
    if (!is_array($filter_iterators)) {
      throw new CsvReaderException('#2 argument should be an array');
    }
    $iterator = $inner_iterator;
    foreach ($filter_iterators as $class_name => $constructor_option) {
      if (!is_array($constructor_option)) {
        $constructor_option = array();
      }
      $real_class_name = $this->find_class($class_name, "Filter");
      if (!$real_class_name) {
        throw new CsvReaderException('filter class not found: '.$class_name);
      }
      $iterator = new $real_class_name($iterator, $constructor_option);
      if (!$iterator instanceof Text_CsvReader_Filter) {
        throw new CsvReaderException('instance does not implements Text_CsvReader_Filter: '.$class_name);
      }
    }
    return $iterator;
  }
  protected function prepareInstances($option_name)
  {
    $classes = $this->getOption($option_name);
    $instances = array();
    $ancestor_class_name = 'Text_CsvReader_'.$option_name;
    foreach ($classes as $class_name => $constructor_option) {
      $real_class_name = $this->find_class($class_name, $option_name);
      if (!$real_class_name) {
        throw new CsvReaderException($option_name.' class not found: '.$class_name);
      }
      $instance = new $real_class_name($constructor_option);
      if (!$instance instanceof $ancestor_class_name) {
        throw new CsvReaderException(sprintf('instance does not implements %s: %s', $ancestor_class_name, $real_class_name));
      } else {
        $instances[] = $instance;
      }
    }
    return $instances;
  }
  protected function find_class($class_name, $subdir_name = '')
  {
    if (class_exists($class_name)) {
      return $class_name;
    }
    $modified_class_name = sprintf("Text_CsvReader_%s",$class_name);
    if (class_exists($modified_class_name)) {
      return $modified_class_name;
    }
    if ($subdir_name) {
      $modified_class_name = sprintf("Text_CsvReader_%s_%s",$subdir_name,$class_name);
      if (class_exists($modified_class_name)) {
        return $modified_class_name;
      }
    }
    return '';
  }
  public function processSheet($enable_writers)
  {
    $writers = $this->writers;
    if ($enable_writers === false) {
      $writers = array();
    }
    foreach ($writers as $writer) {
      $writer->initialize();
    }
    try {
      foreach ($this->iterator as $values) {
        foreach ($writers as $writer) {
          $writer->write($values);
        }
      }
    } catch (CsvReaderValidatorException $e) {
      $this->validatorException = $e;
      foreach ($writers as $writer) {
        $writer->rollback();
      }
      return false;
    }
    foreach ($writers as $writer) {
      $writer->finalize();
    }
    return true;
  }
  public function hasError() {
    return isset($this->validatorException);
  }
  public function getErrors() {
    return isset($this->validatorException) ? $this->validatorException->getErrors() : array();
  }

}