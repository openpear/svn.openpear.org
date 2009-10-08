<?php
abstract class Text_CsvReader_Base
{
  protected
    $requiredOptions = array(),
    $defaultMessages = array(),
    $defaultOptions  = array(),
    $messages        = array(),
    $options         = array(),
    $targetOptions   = array(),
    $targetColumns   = array();

  /**
   * Constructor.
   *
   * @param Iterator $iterator   An iterator
   * @param array $options       An array of options
   * @param array $messages      An array of error messages
   */
  public function __construct($options = array(), $messages = array())
  {
    if (!is_array($options) || !is_array($messages)) {
      throw new CsvReaderException('argumtnts should be array type');
    }
    $this->configure($options, $messages);

    $this->setDefaultOptions($this->getOptions());
    $this->setDefaultMessages($this->getMessages());

    $currentOptionKeys = array_keys($this->options);
    $optionKeys = array_keys($options);

    // check option names
    if ($diff = array_diff($optionKeys, array_merge($currentOptionKeys, $this->requiredOptions))) {
        throw new CsvReaderException(sprintf('%s does not support the following options: \'%s\'.', get_class($this), implode('\', \'', $diff)));
    }
    // check required options
    if ($diff = array_diff($this->requiredOptions, array_merge($currentOptionKeys, $optionKeys))) {
        throw new CsvReaderException(sprintf('%s requires the following options: \'%s\'.', get_class($this), implode('\', \'', $diff)));
      }

    $this->options  = array_merge($this->options, $options);
    $this->messages = array_merge($this->messages, $messages);
    $this->prepareTargetColumns();
  }

  /**
   * Configures the current filter.
   *
   * This method allows each filter to add options and error messages
   * during filter creation.
   *
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see __construct()
   */
  protected function configure($options = array(), $messages = array())
  {
  }

  /**
   * Returns an error message given an error code.
   *
   * @param  string $name  The error code
   *
   * @return string The error message, or the empty string if the error code does not exist
   */
  public function getMessage($name)
  {
    return isset($this->messages[$name]) ? $this->messages[$name] : '';
  }

  /**
   * Adds a new error code with a default error message.
   *
   * @param string $name   The error code
   * @param string $value  The error message
   */
  public function addMessage($name, $value)
  {
    $this->messages[$name] = $value;
  }

  /**
   * Changes an error message given the error code.
   *
   * @param string $name   The error code
   * @param string $value  The error message
   */
  public function setMessage($name, $value)
  {
    if (!in_array($name, array_keys($this->messages))) {
        throw new CsvReaderException(sprintf('%s does not support the following error code: \'%s\'.', get_class($this), $name));
    }

    $this->messages[$name] = $value;
  }

  /**
   * Returns an array of current error messages.
   *
   * @return array An array of messages
   */
  public function getMessages()
  {
    return $this->messages;
  }

  /**
   * Changes all error messages.
   *
   * @param array $values  An array of error messages
   */
  public function setMessages($values)
  {
    $this->messages = $values;
  }

  /**
   * Gets an option value.
   *
   * @param  string $name  The option name
   *
   * @return mixed  The option value
   */
  public function getOption($name, $second_level_key = null)
  {
    if (!isset($this->options[$name])) {
      return '';
    } else {
      $value = $this->options[$name];
      if ($second_level_key !== null && is_array($value)) {
        return isset($value[$second_level_key]) ? $value[$second_level_key] : '';
      }
      return $value;
    }
  }

  /**
   * Adds a new option value with a default value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The default value
   */
  public function addOption($name, $value = null)
  {
    $this->options[$name] = $value;
  }

  /**
   * Changes an option value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The value
   */
  public function setOption($name, $value)
  {
    if (!in_array($name, array_merge(array_keys($this->options), $this->requiredOptions))) {
      throw new CsvReaderException(sprintf('%s does not support the following option: \'%s\'.', get_class($this), $name));
    }
    $this->options[$name] = $value;
    $this->prepareTargetColumns();
  }

  /**
   * Returns true if the option exists.
   *
   * @param  string $name  The option name
   *
   * @return bool true if the option exists, false otherwise
   */
  public function hasOption($name, $second_level_key = null)
  {
    if (!isset($this->options[$name])) {
      return false;
    } else {
      $value = $this->options[$name];
      if ($second_level_key !== null && is_array($value)) {
        return isset($value[$second_level_key]);
      }
      return true;
    }
  }

  /**
   * Returns all options.
   *
   * @return array An array of options
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Changes all options.
   *
   * @param array $values  An array of options
   */
  public function setOptions($values)
  {
    $this->options = $values;
  }

  /**
   * Returns default messages for all possible error codes.
   *
   * @return array An array of default error codes and messages
   */
  public function getDefaultMessages()
  {
    return $this->defaultMessages;
  }

  /**
   * Sets default messages for all possible error codes.
   *
   * @param array $messages  An array of default error codes and messages
   */
  protected function setDefaultMessages($messages)
  {
    $this->defaultMessages = $messages;
  }

  /**
   * Returns default option values.
   *
   * @return array An array of default option values
   */
  public function getDefaultOptions()
  {
    return $this->defaultOptions;
  }

  /**
   * Sets default option values.
   *
   * @param array $options  An array of default option values
   */
  protected function setDefaultOptions($options)
  {
    $this->defaultOptions = $options;
  }
  protected function getTargetColumns($value) {
    return $this->targetColumns ? $this->targetColumns : array_keys($value);
  }
  protected function prepareTargetColumns() {
    $columns = array();
    if ($this->hasOption('target') && is_array($this->getOption('target'))) {
      $columns = $this->getOption('target');
    } elseif (is_array($this->targetOptions) && $this->targetOptions !== array()) {
      foreach ($this->targetOptions as $optionName) {
        // 配列のキーに
        if ($this->hasOption($optionName) && is_array($this->getOption($optionName))) {
          $columns = $columns + $this->getOption($optionName);
        }
      }
      $columns = array_keys($columns);
    }
    $this->targetColumns = $columns;
  }

}
