<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(10, new lime_output_color);

$reader = new Text_CsvReader();
$config = array(
                'simple' =>
                array(
                      'reader' => array('Reader_Csv' => array('file' => dirname(__FILE__)."/csv/simple.csv")),
                      'prefilter' => array(
                                        //'Filter_Header' => null,
                                        'Filter_Trim' => null,
                                        'Grepper_Regex' => array('pattern'=>array(0=>'/^[abc]{3}$/')),
                                        ),
                      'validator' => array(
                                           'Validator_NColumn' => array('ncolumn'=>2),
                                           'Validator_String' => array(
                                                                       'min_length'=>array(0=>1, 1=>3),
                                                                       'max_length'=>array(0=>3, 1=>8)
                                                                       ),
                                           'Validator_Regex' => array('pattern'=>array(
                                                                                       0=>'/^[abc]{3}$/',
                                                                                       1=>'/^\d+$/',
                                                                                       )),
                                           ),
                      'writer' => array('Writer_Variable' => array('name' => 'test_Text_CsvReader',
                                                                   'key'=> 0,
                                                                   'value'=> 1,
                                                                   )),
                      ),
                );

/* ============================== */

$lime->diag("filter chain");

$reader->configure($config);
$reader->process('simple');

$lime->ok($reader->getErrors('simple') === array(), 'process finished without error');
$reader->showErrors('simple');

$output = $reader->getArray('test_Text_CsvReader');
$lime->ok(sizeof($output) === 1, 'array size: '. sizeof($output));
$lime->ok($output["abc"] === "123", 'variable');
$lime->ok($reader->getArrayValue('test_Text_CsvReader',"abc") === "123", 'variable');

/* ============================== */

$lime->diag("Exception");

$noSuchClassConfig = $config;
$noSuchClassConfig['simple']['reader'] = array('NoSuchReader' => null);


try {
  $reader->configure($noSuchClassConfig);
  $reader->process('simple');
  $lime->fail('reader class not found');
}
catch (CsvReaderException $e) {
  $lime->pass('reader class not found');
}

/* ============================== */

$noSuchClassConfig = $config;
$noSuchClassConfig['simple']['prefilter'] = array('NoSuchFilter' => null);

try {
  $reader->configure($noSuchClassConfig);
  $reader->process('simple');
  $lime->fail('filter class not found');
}
catch (CsvReaderException $e) {
  $lime->pass('filter class not found');
}

/* ============================== */

$noSuchClassConfig = $config;
$noSuchClassConfig['simple']['writer'] = array('NoSuchWriter' => null);

try {
  $reader->configure($noSuchClassConfig);
  $reader->process('simple');
  $lime->fail('writer class not found');
}
catch (CsvReaderException $e) {
  $lime->pass('writer class not found');
}

/* ============================== */

$conflictInterfaceConfig = $config;
$conflictInterfaceConfig['simple']['reader'] = $config['simple']['writer'];

try {
  $reader->configure($conflictInterfaceConfig);
  $reader->process('simple');
  $lime->fail('reader class exists, but interface conflicts');
}
catch (CsvReaderException $e) {
  $lime->pass('reader class exists, but interface conflicts');
}

/* ============================== */

$conflictInterfaceConfig = $config;
$conflictInterfaceConfig['simple']['prefilter'] = $config['simple']['reader'];
try {
  $reader->configure($conflictInterfaceConfig);
  $reader->process('simple');
  $lime->fail('filter class exists, but interface conflicts');
}
catch (CsvReaderException $e) {
  $lime->pass('filter class exists, but interface conflicts');
}

/* ============================== */

$conflictInterfaceConfig = $config;
$conflictInterfaceConfig['simple']['writer'] = $config['simple']['reader'];
try {
  $reader->configure($conflictInterfaceConfig);
  $reader->process('simple');
  $lime->fail('writer class exists, but interface conflicts');
}
catch (CsvReaderException $e) {
  $lime->pass('writer class exists, but interface conflicts');
}

