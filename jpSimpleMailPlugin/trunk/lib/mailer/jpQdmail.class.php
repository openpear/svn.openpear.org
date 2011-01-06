<?php
/**
 * jpQdmail class
 *
 * @package    jpSimpleMailPlugin
 * @subpackage lib
 * @author     brt.river <brt.river@gmail.com>
 * @version    $Id$
 */
class jpQdmail extends jpMailer
{
  public function initialize()
  {
    $this->setMailer(new sfQdmail);
    mb_language('Ja');
  }
  public function setCharset($charset)
  {
    $this->mailer->charset($charset);
  }
  public function getCharset()
  {
    $ret = $this->mailer->charset();
    return $ret['HEADER'];
  }
  public function setPriority($priority)
  {
    $this->mailer->setPriority($priority);
  }
  public function getPriority()
  {
    return $this->mailer->getPriority();
  }
  public function setEncoding($encoding)
  {
    $this->mailer->setEncoding($encoding);
  }
  public function getEncoding()
  {
    return $this->mailer->getEncoding();
  }
  public function setSender($address, $name = null)
  {
    return $this->mailer->setSender($address, $name);
  }
  public function getSender()
  {
    return $this->getReturnPath();
  }
  public function setReturnPath($address)
  {
    $this->mailer->setSender($address);
  }
  public function getReturnPath()
  {
    return $this->mailer->getSender();
  }
  public function addAddress($address, $name = null)
  {
    $this->addTo($address, $name);
  }
  public function addTo($address, $name = null)
  {
    if ($name == null) {
      list($address, $name) = jpSimpleMail::splitAddress($address);
    }
    $this->mailer->addAddress($address, $name);
  }
  public function setFrom($address, $name = null)
  {
    if ($name == null) {
      list($address, $name) = jpSimpleMail::splitAddress($address);
    }
    $this->mailer->setFrom($address, $name);
  }
  public function addCc($address, $name = null)
  {
    if ($name == null) {
      list($address, $name) = jpSimpleMail::splitAddress($address);
    }
    $this->mailer->AddCc($address, $name);
  }
  public function addBcc($address, $name = null)
  {
    if ($name == null) {
      list($address, $name) = jpSimpleMail::splitAddress($address);
    }
    $this->mailer->AddBcc($address, $name);
  }
  public function setSubject($subject)
  {
    $this->mailer->setSubject($subject);
  }
  public function setBody($body)
  {
    $this->mailer->setBody($body);
  }
  public function setAltBody($body)
  {
    $this->mailer->setAltBody($body);
  }
  public function addReplyTo($address, $name = null)
  {
    if ($name == null) {
      list($address, $name) = jpSimpleMail::splitAddress($address);
    }
    $this->mailer->AddReplyTo($address, $name);
  }
  public function getTo()
  {
    return $this->mailer->getSender();
  }
  public function getFrom()
  {
    // if not set, email validation erro is occured.
    $from = $this->mailer->from();
    return (isset($from[0]['mail']))? $from[0]['mail']: "";
  }
  public function getSubject()
  {
    $arr = $this->mailer->getSubject();
    return (isset($arr['CONTENT']))? $arr['CONTENT'] : "";
  }
  public function getBody()
  {
    return $this->mailer->getBody();
  }
  public function cleararTo()
  {
    $this->mailer->ClearAddresses();
  }
  public function clearCcs()
  {
    $this->mailer->ClearCcs();
  }
  public function clearBccs()
  {
    $this->mailer->ClearBccs();
  }
  public function clearReplyTo()
  {
    $this->mailer->ClearReplyTos();
  }
  public function send()
  {
    if (!$this->mailer->send()) {
      throw new jpSendMailException('error in send email');
    }
    return true;
  }
}