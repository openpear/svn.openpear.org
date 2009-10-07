<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(17, new lime_output_color);

/* ============================== */

$lime->diag("CSV: simple.csv");

$it = new Text_CsvReader_Reader_CSV(array('file'
                                          => dirname(__FILE__)."/csv/simple.csv"));
$output = array();
foreach ($it as $values) {
  $output[] = $values;
}

$lime->ok(sizeof($output) === 4, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("abc","123 "), '0th element');
$lime->ok($output[1] === array("345",'ab\\\\c'), '1st element');
$lime->ok($output[2] === array('\\n2AB','XY"Z8'), '2nd element');
$lime->ok($output[3] === array('\".\"','a"b', "'abc'","'abc'"), '3rd element');

/* ============================== */

$lime->diag("CSV with no line break: no_line_break.csv");

$it = new Text_CsvReader_Reader_CSV(array('file'
                                          => dirname(__FILE__)."/csv/no_line_break.csv"));
$output = array();
foreach ($it as $values) {
  $output[] = $values;
}

$lime->ok(sizeof($output) === 1, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("no","line","break"), '0th element');

/* ============================== */

$lime->diag("Shift_JIS: sjis.csv");

$it = new Text_CsvReader_Reader_CSV(array('file'
                                          => dirname(__FILE__)."/csv/sjis.csv",
                                          'charset' => 'SJIS-win'));
$output = array();
foreach ($it as $values) {
  $output[] = $values;
}

$lime->ok(sizeof($output) === 1, 'array size: '. sizeof($output));
$lime->ok($output[0] === array('えすじす','エスジス','１','表'), '0th element');

/* ============================== */

$lime->diag("UTF-8: utf8.csv");

$it = new Text_CsvReader_Reader_CSV(array('file'
                                          => dirname(__FILE__)."/csv/utf8.csv"));

$output = array();
foreach ($it as $values) {
  $output[] = $values;
}

$lime->ok(sizeof($output) === 1, 'array size: '. sizeof($output));
$lime->ok($output[0] === array('ゆーてぃーえふ８','表'), '0th element');

/* ============================== */

$lime->diag("EUC-JP: eucjp.csv");

$it = new Text_CsvReader_Reader_CSV(array('file'
                                          => dirname(__FILE__)."/csv/eucjp.csv",
                                          'charset' => 'eucJP-win'));
$output = array();
foreach ($it as $values) {
  $output[] = $values;
}

$lime->ok(sizeof($output) === 1, 'array size: '. sizeof($output));
$lime->ok($output[0] === array('いーゆーしー','表'), '0th element');

/* ============================== */

$lime->diag("TSV: complex.tsv");

$it = new Text_CsvReader_Reader_CSV(array('file'
                                          => dirname(__FILE__)."/csv/complex.tsv",
                                          'delimiter' => "\t"));
$output = array();
foreach ($it as $values) {
  $output[] = $values;
}

$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("1\n2","3,4","5\t6"), '0th element');
$lime->ok($output[1] === array("7","8"), '1st element');

/* ============================== */

$lime->diag("Exception");

try
{
  $it = new Text_CsvReader_Reader_Csv(array('file'
                                            => dirname(__FILE__)."/csv/nosuchfile"));
  $it->rewind();
  $lime->fail('input file not exists.');
}
catch (CsvReaderException $e)
{
  $lime->pass('input file not exists.');
}
