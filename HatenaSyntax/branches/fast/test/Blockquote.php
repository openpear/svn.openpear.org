<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$p = HatenaSyntax_Locator::it()->blockquote;

//--

$context = PEG::context(">>\na\n<<");
$result = $p->parse($context);
$lime->is($result->getType(), 'blockquote');
$data = $result->getData();
$lime->is($data['url'], false);
$lime->ok(is_array($data['body']));

//--

$context = PEG::context(">http://google.com>\na\n<<");
$result = $p->parse($context);
$lime->is($result->getType(), 'blockquote');
$data = $result->getData();
$lime->is($data['url'], 'http://google.com');
$lime->ok(is_array($data['body']));