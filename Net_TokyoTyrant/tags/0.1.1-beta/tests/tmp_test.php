<?php
require_once dirname(dirname(__FILE__)) . '/Net/TokyoTyrant.php';

$tt = new Net_TokyoTyrant();
$key = 'keytest';
$data = 'the test data';
$key2 = 'keytest2';
$data2 = 'the test2 data';
$count_key = 'count';
$extname = 'echo';
$error = null;
try {
    $tt->connect('dummy', 1978);
} catch (Net_TokyoTyrantException $e) {
    $error = $e->getMessage();
}
assert(is_string($error) && strlen($error) > 1);


$tt->connect('localhost', 1978);

assert($tt->vanish() === true);
assert($tt->put($key, $data) === true);
$getdata = $tt->get($key);
assert($getdata === $data);
  
assert($tt->putkeep($key, $data . 'keep') === false);
$getdata = $tt->get($key);
assert($getdata === $data);
$tt->out($key);
assert($tt->putkeep($key, $data . 'keep') === true);
$getdata = $tt->get($key);
assert($getdata === $data . 'keep');

assert($tt->put($key, $data) === true);
$getdata = $tt->get($key);
assert($getdata === $data);
assert($tt->putcat($key, $data) === true);
$getdata = $tt->get($key);
assert($getdata === $data . $data);


assert($tt->put($key, $data) === true);
assert($tt->putrtt($key, $data, 2) === true);
$getdata = $tt->get($key);
assert($getdata === substr($data, strlen($data) - 2, 2));


assert($tt->out($key) === true);
$getdata = $tt->get($key);
assert($getdata === false);

assert($tt->put($key, $data));
assert($tt->put($key2, $data2));
assert(count($tt->mget(array($key, $key2))) === 2);
assert(count($tt->fwmkeys('key', 2)) === 2);
assert($tt->vsize($key) === strlen($data));
assert($tt->vanish() === true);
assert($tt->iterinit() === true);
assert($tt->iternext() === false);

assert($tt->put($key, $data));
assert($tt->iterinit() === true);
assert($tt->iternext() === $key);
assert($tt->iternext() === false);

assert($tt->addint($count_key, 1) === 1);
assert($tt->addint($count_key, 2) === 3);
assert($tt->addint($count_key, -2) === 1);
assert($tt->putint($count_key, 1));
assert($tt->getint($count_key) === 1);
assert($tt->addint($count_key, 1) === 2);
assert($tt->getint($count_key) === 2);
assert($tt->addint($count_key, -3) === -1);

//$value = 'data';
//assert($tt->ext($extname, $key, $value) === $value);
//assert($tt->ext($extname, $key, $value, Net_TokyoTyrant::RDBXOLCKNON) === $value);
//assert($tt->ext($extname, $key, $value, Net_TokyoTyrant::RDBXOLCKREC) === $value);
//assert($tt->ext($extname, $key, $value, Net_TokyoTyrant::RDBXOLCKGLB) === $value);


assert($tt->sync() === true);
assert(is_array($tt->size()));
assert(is_array($tt->rnum()));

assert($tt->copy('/tmp/test.net_tokyotyrant.db') === true);
assert(file_exists('/tmp/test.net_tokyotyrant.db') === true);
assert(strlen($tt->stat()) > 1);
$tt->close();
