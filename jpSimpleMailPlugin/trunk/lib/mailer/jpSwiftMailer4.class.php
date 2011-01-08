<?php
/**
 * jpSwiftMailer4 class
 * 
 *  this class is for SwiftMailer ver4
 *  require Jp_Swift_Mime_Headers_UnstructuredHeader
 *    ref: http://www.kuzilla.co.jp/article.php/20100301symfony
 *
 * @package    jpSimpleMailPlugin
 * @subpackage lib
 * @author     brt.river <brt.river@gmail.com>
 * @version    $Id: jpSwiftMailer4.class.php 1725 2010-03-22 13:32:49Z brtriver $
 */
class jpSwiftMailer4 extends jpMailer
{
  public
    $address = "",
    $message = "",
    $from = "";
  public function initialize()
  {
    if (sfContext::hasInstance() and sfContext::getInstance()->getMailer()) {
      $mailer = sfContext::getInstance()->getMailer();
    } else {
      require_once sfConfig::get('sf_symfony_lib_dir') .'/vendor/swiftmailer/swift_required.php';
      $transport = Swift_MailTransport::newInstance();
      $mailer = Swift_Mailer::newInstance($transport);
    }

    $this->setMailer($mailer);
    mb_language('Ja');
    mb_internal_encoding(sfConfig::get('app_jpSimpleMail_encoding', 'utf-8'));
    $this->message = Swift_Message::newInstance();
    $this->message->getHeaders()->remove('Subject');
    $subjectHeader = new jp_Swift_Mime_Headers_UnstructuredHeader('Subject',
      new Swift_Mime_HeaderEncoder_Base64HeaderEncoder());
    $this->message->getHeaders()->set($subjectHeader);
    $this->message->setContentType('text/plain');
    $this->setCharset('iso-2022-jp');
    $this->message->setEncoder(Swift_Encoding::get7BitEncoding());
  }
  public function setCharset($charset)
  {
    $this->message->setCharset($charset);
  }
  public function getCharset()
  {
    return $this->message->getCharset();
  }
  public function setPriority($priority)
  {
    $this->message->setPriority($priority);
  }
  public function getPriority()
  {
    return $this->message->getPriority();
  }
  public function setEncoding($encoding)
  {
    $this->message->getHeaders()->get('Content-Transfer-Encoding')->setValue($encoding);
  }
  public function getEncoding()
  {
    return $this->message->getHeaders()->get('Content-Transfer-Encoding')->getValue();
  }
  public function setSender($address, $name = null)
  {
    if (!$address) {
      return;
    }
    if ($name == null) {
      list($address, $name) = jpSimpleMail::splitAddress($address);
    }
    $this->message->setReturnPath($address);
  }
  public function getSender()
  {
    return $this->message->getReturnPath();
  }
  public function setReturnPath($address)
  {
    $this->message->setReturnPath($address);
  }
  public function getReturnPath()
  {
    return $this->message->getReturnPath();
  }
  public function addAddress($address, $name = null)
  {
    $this->message->addTo($address, $name);
  }
  public function addTo($address, $name = null)
  {
    if ($name == null)
    {
      list($address, $name) = jpSimpleMail::splitAddress($address);
    }
    $name = jpSimpleMail::mb_encode_mimeheader($name);
    $this->message->addTo($address, $name);
  }
  public function setFrom($address, $name = null)
  {
    if (!$address) {
      return;
    }
    if ($name == null)
    {
      list($address, $name) = jpSimpleMail::splitAddress($address);
    }
    $this->mailer->From     = $address;
    $name = jpSimpleMail::mb_encode_mimeheader($name);
    $this->message->setFrom($address, $name);
  }
  public function addCc($address, $name = null)
  {
    if ($name == null) {
      list($address, $name) = jpSimpleMail::splitAddress($address);
    }
    $name = jpSimpleMail::mb_encode_mimeheader($name);
    $this->message->addCc($address, $name);
  }
  public function addBcc($address, $name = null)
  {
    if ($name == null) {
      list($address, $name) = jpSimpleMail::splitAddress($address);
    }
    $name = jpSimpleMail::mb_encode_mimeheader($name);
    $this->message->addBcc($address, $name);
  }
  public function setSubject($subject)
  {
    $this->message->setSubject($subject);
  }
  public function setBody($body)
  {
    $body = mb_convert_encoding($body, $this->getCharset(), mb_internal_encoding());
    $this->message->setBody($body);
  }
  public function setAltBody($body)
  {
  }
  public function addReplyTo($address, $name = null)
  {
    if (!$address) {
      return;
    }
    if ($name == null) {
      list($address, $name) = jpSimpleMail::splitAddress($address);
    }
    $name = jpSimpleMail::mb_encode_mimeheader($name);
    $this->message->setReplyTo($address, $name);
  }
  public function getFrom()
  {
    $from = $this->message->getFrom();
    return key($from);
  }
  public function getSubject()
  {
    return $this->message->getSubject();
  }
  public function getBody()
  {
    return mb_convert_encoding($this->message->getBody(), mb_internal_encoding(), $this->getCharset());
  }
  public function clearTo()
  {
    $this->message->setTo(null);
  }
  public function clearCcs()
  {
    $this->message->setCc(null);
  }
  public function clearBccs()
  {
    $this->message->setBcc(null);
  }
  public function clearReplyTo()
  {
    $this->message->setReplyTo(null);
  }
  public function send()
  {
    try {
      $result = $this->mailer->send($this->message);
      if (!$result) throw new Exception('Failures: Cannot send E-mail');
      return true;
    } catch ( Exception $e) {
      throw new jpSendMailException($e);
    }
  }
}

//@require 'Swift/Mime/Headers/AbstractHeader.php';
//@require 'Swift/Mime/HeaderEncoder.php';

/**
 * 日本語(ISO-2022-JP)用メールヘッダクラス
 * @package jpSimpleMailPlugin
 * @subpackage Mime
 * @author kawaguchi
 * @url http://www.kuzilla.co.jp/
 */
class Jp_Swift_Mime_Headers_UnstructuredHeader
  extends Swift_Mime_Headers_UnstructuredHeader
{
  // override
  public function getFieldBody()
  {
    if (!$this->getCachedValue())
    {
      // ISO-2022-JP対応
      if (strcasecmp($this->getCharset(), 'iso-2022-jp') === 0)
      {
        $this->setCachedValue(jpSimpleMail::mb_encode_mimeheader(( $this->getValue())));
      } else {
        parent::getFieldBody();
      }
    }
    return $this->getCachedValue();
  }  
}
