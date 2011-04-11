<?php
include(dirname(__FILE__) . '/../bootstrap/unit.php');

$t = new lime_test(26, new lime_output_color());
// send mail
$t->diag('check method');
$mailer = jpSimpleMail::create('SwiftMailer4');
$t->isa_ok($mailer, 'jpSwiftMailer4', 'created instance is correct');
// charset
$old = $mailer->getCharset();
$mailer->setCharset('utf-8');
$t->is($mailer->getCharset(), 'utf-8', 'test Charset');
$mailer->setCharset($old);
// priority
$old = $mailer->getPriority();
$mailer->setPriority('1');
$t->is($mailer->getPriority(), '1', 'test Priority');
$mailer->setPriority($old);
// encoding
$old = $mailer->getEncoding();
$mailer->setEncoding('7bit');
$t->is($mailer->getEncoding(), '7bit', 'test Encoding');
$mailer->setEncoding($old);
// return path
$old = $mailer->getReturnPath();
$mailer->setReturnPath($params['from']);
$t->is($mailer->getReturnPath(), $params['from'], 'test Return Path');
$mailer->setReturnPath($old);
// addTo
$t->is($mailer->addTo($_SERVER['SF_TEST_TO_ADDRESS']), null, 'test to call addTo method');
$t->is($mailer->addTo($_SERVER['SF_TEST_TO_ADDRESS'], $params['to_name']), null, 'test to call addTo method with name');
// from
$old = $mailer->getFrom();
$mailer->setFrom($params['from'], $params['from_name']);
$t->is($mailer->getFrom(), $params['from'], 'test From');
$mailer->setFrom($old);
//addCc
$t->is($mailer->addCc($_SERVER['SF_TEST_TO_ADDRESS']), null, 'test to call addCc method');
$t->is($mailer->addCc($_SERVER['SF_TEST_TO_ADDRESS'], $params['to_name']), null, 'test to call addCc method with name');
//addBcc
$t->is($mailer->addBcc($_SERVER['SF_TEST_TO_ADDRESS']), null, 'test to call addBcc method');
$t->is($mailer->addBcc($_SERVER['SF_TEST_TO_ADDRESS'], $params['to_name']), null, 'test to call addBcc method with name');
// Subject
$old = $mailer->getSubject();
$mailer->setSubject($params['subject']);
$t->is($mailer->getSubject(), $params['subject'], 'test Subject');
$mailer->setSubject($old);
// body
$old = $mailer->getBody();
$mailer->setBody($params['body']);
$t->is($mailer->getbody(), $params['body'], 'test body');
$mailer->setBody($old);
//addReplyTo
$t->is($mailer->addReplyTo($params['from']), null, 'test to call addReplyTo method');
$t->is($mailer->addReplyTo($params['from'], $params['from_name']), null, 'test to call addReplyTo method with name');
// clearTo
$t->is($mailer->clearTo(), null, 'test to call clearTo method');
// clearCc
$t->is($mailer->clearCc(), null, 'test to call clearCc method');
// clearBcc
$t->is($mailer->clearBcc(), null, 'test to call clearBcc method');
// clearReplyTo
$t->is($mailer->clearReplyTo(), null, 'test to call clearReplyTo method');
// sender ( only this class)
$old = $mailer->getSender();
$mailer->setSender($params['from'], $params['from_name']);
$t->is($mailer->getSender(), $params['from'], 'test Sender');
$mailer->setSender($old);
// addAddress (only this class)
$t->is($mailer->addAddress($_SERVER['SF_TEST_TO_ADDRESS']), null, 'test to call addAddress method');
$t->is($mailer->addAddress($_SERVER['SF_TEST_TO_ADDRESS'], $params['to_name']), null, 'test to call addAddress method with name');
// check RFC Compliance mail throw exception
try {
  $mailer->setFrom('testtesttest.@gmail.com', 'test user for gmail');
} catch (Exception $e) {
  $t->isa_ok($e, 'Swift_RfcComplianceException', 'RFC incompiance mail throws Exception.');
}
$t->is($mailer->clearTo(), null, 'test to call clearTo method');
$mailer->addTo('testtesttest.@docomo.ne.jp', null, 'test user for docomo');
$t->is($mailer->message->getTo(), array('testtesttest.@docomo.ne.jp' => ''), 'RFC incompianct mail address has to pass here.');
$mailer->setFrom('testtes..ttest@ezweb.ne.jp', 'test user for docomo');
$t->is($mailer->getFrom(), 'testtes..ttest@ezweb.ne.jp', 'RFC incompianct mail address has to pass here.');
