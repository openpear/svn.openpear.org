<?php
require_once 'Console/CommandLine.php';

class PHP_Obfuscator_CommandLineOptions
{
    public function __construct(array $argv) {
        $parser = new Console_CommandLine(array(
            'description' => 'obfuscate php script.',
            'version'     => '0.1.0'
        ));

        $parser->addOption('verbose', array(
            'long_name'   => '--verbose',
            'action'      => 'StoreTrue',
            'description' => 'turn on verbose output'
        ));

        $parser->addOption('filter', array(
            'multiple' => true,
            'short_name'  => '-t',
            'long_name'   => '--filter',
            'action'      => 'StoreArray',
            'description' => 'a list of filters. specify \'XXXX\' if use PHP_Obfuscator_Filter_XXXXFilter',
        ));

        $parser->addOption('encoder', array(
            'multiple' => true,
            'short_name'  => '-e',
            'long_name'   => '--encoder',
            'action'      => 'StoreArray',
            'description' => 'encoder names. specify \'XXXX\' if use PHP_Obfuscator_Encoder_XXXXEncoder',
        ));

        $parser->addArgument('file', array(
            'description' => 'the script file name to obfuscate'
        ));

        try {
            $result = $parser->parse();
            $this->options = $result->options;
            $this->filename = $result->args;
        } catch (Exception $exc) {
            $parser->displayError($exc->getMessage());
        }
    }

    public function getFileName() {
        return $this->filename['file'];
    }

    public function isVerbose() {
        return $this->options['verbose'] === true;
    }

    public function getFilters() {
        return is_null($this->options['filter']) ? array() : $this->options['filter'];
    }

    public function getEncoders() {
        return is_null($this->options['encoder']) ? array() : $this->options['encoder'];
    }
}
