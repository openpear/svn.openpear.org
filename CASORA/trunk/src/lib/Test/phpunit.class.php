<?php

class Test_phpunit implements iTest
{
    public function run( $test_dir, $path, $action )
    {
        require_once('PHPUnit/Framework.php');
        require_once('PHPUnit/TextUI/TestRunner.php');

        $file_name = "$test_dir$path/". ucfirst($action) .'.class.php';
        require_once( $file_name );

        $name = "";
        foreach( explode('/',$path) as $path ) $name .= ucfirst($path);
        $class_name = $name. ucfirst($action) .'Test';

        if ( ! class_exists($class_name) ) {
            throw new Exception( "$class_name class not exists." );
        }

        $suite = new PHPUnit_Framework_TestSuite( $class_name );

        print "<PRE>";
        $result = PHPUnit_TextUI_TestRunner::run( $suite );
        print "</PRE>";

        return;
    }

    public function trigger()
    {

    }
}
