<?php
/**
  * Keires_Feed
  *
  * Feed Parser like XML_Feed_Parser
  *
  * PHP version 5
  *
  * LICENSE: This source file is subject to version 3.0 of the PHP license
  * that is available through the world-wide-web at the following URI:
  * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
  * the PHP License and are unable to obtain it through the web, please
  * send a note to license@php.net so we can mail you a copy immediately.
  *
  * @category   XML
  * @package    Keires_Feed
  * @author     KOYAMA Tetsuji <koyama@hoge.org>
  * @copyright  2008-2009 KOYAMA Tetsuji
  * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
  * @version    svn: $Id$
  * @link       http://openpear.org/package/Keires_Feed
  * @since      File available since Release 0.1
  *
  * based on Rasmus Lerdorf's simple_rss.php
  *  ( http://lerdorf.com/php/simple_rss.phps
  */

require_once 'Keires/Feed.php';

class Keires_FeedParser {

    protected $xmlversion = '1.0';
    protected $encoding = null;
    protected $xml = null;
    protected $feed = null;

    public function __construct($xml, $options = null) {
        $this->xml = $xml;
        $this->analyze();
    }

    public function getFeed() {
        return $this->feed;
    }

    static protected function firstLine(&$text) {
        $p1 = strpos($text, "\r");
        $p2 = strpos($text, "\n");
        $p = FALSE;

        if ($p1 === FALSE) {
            $p = $p2;
        } else {
            if ($p2 === FALSE) {
                $p = $p1;
            } else {
                $p = min($p1, $p2);
            }
        }
        if ($p === FALSE) {
            // no or long length line
            return substr($text, 0, 256);
        }
        return substr($text, 0, $p);
    }

    protected function analyze() {
        $xml = $this->xml;

        $first_line = $this->firstLine($xml);
        $nmatch = preg_match('!<?xml version=[\'"](.+)[\'"] encoding=[\'"](.+)[\'"]\s*\?>!i',
                             $first_line, $matches);
        if ($nmatch != 1) {
            throw new Keires_Exception('Invalid XML document',
                                       KEIRES_ERROR_INVALID_XML);
        }

        $this->xmlversion = $matches[1];
        $this->encoding = $matches[2];
    }

    public function parse() {
        $options = array(
            'version' => $this->xmlversion,
            'encoding' => $this->encoding,
            );
        $feed = self::parseXML($this->xml, $options);
        $this->feed = $feed;
    }

    public function type() {
        if (empty($this->feed)) {
            throw new Keires_Exception('feed is not parsed yet',
                                       KEIRES_ERROR_UNPARSED);
        }
        return $this->feed['_type'];
    }

    public function items() {
        if (empty($this->feed)) {
            throw new Keires_Exception('feed not parsed yet',
                                       KEIRES_ERROR_UNPARSED);
        }
        return $this->feed['item'];
    }

    static public function feedType($dom) {
        $type = null;
        foreach ($dom->childNodes as $child) {
            $node = $child->nodeName;
            switch ($node) {
            case 'rss':
                $type = 'rss';
                break;
            case 'feed':
                $type = 'atom';
                break;
            default:
                if (strncmp($node, 'rdf', 3) === 0) {
                    $type = 'rss';
                }
            }
            if (!empty($type)) {
                break;
            }
        }
        return $type;
    }

    static public function parseXML($input_xml, $options) {
        $version = array_val($options, 'version', '1.0');
        $encoding = array_val($options, 'encoding', 'UTF-8');

        $dom = new DOMDocument($version, $encoding);

        $result = $dom->loadXML($input_xml);
        if ($result === FALSE) {
            throw new Keires_Exception('DOMDocument load error',
                                       KEIRES_ERROR_DOM_LOADING);
        }
        $feedtype = self::feedType($dom);

        // Pick out the namespaces that apply to this doc.
        // We need to do this from DOM because simplexml does't see the
        // special xmlns attributes because of the way libxml2 handles them.
        $xpath = new DOMXPath($dom);
        $ns = array(''=>NULL);
        foreach($xpath->query("namespace::*") as $v) {
            if($v->localName!='xml') $ns[$v->localName] = $v->nodeValue;
        }
        $atom10 = $atom03 = false;
        if ($feedtype != 'rss') {
            if (in_array('http://www.w3.org/2005/Atom', $ns)) {
                $atom10 = true;
                unset($ns['xmlns']);
            } else if (in_array('http://purl.org/atom/ns#', $ns)) {
                $atom03 = true;
                unset($ns['xmlns']);
            }
        }

        // Ok, now we can switch to simplexml
        $xml = simplexml_import_dom($dom);
        if ($xml === null) {
            throw new Keires_Exception('XML format error',
                                       KEIRES_ERROR_INVALID_XML);
        }
        $feed = array();

        // Pull out the root attributes - usually just version
        foreach($xml->attributes() as $k=>$v) $feed[$k] = (string)$v;

        // We will deal with the items separately, so start by only looking
        // at the stuff leading up to the items checking each namespace.
        $rss1 = false;
        if ($atom10 || $atom03) {
            $top = $xml;
        } else {
            $top = $xml->channel;
        }
        foreach($ns as $alias=>$uri) foreach($top->children($uri) as $key=>$val) {
            if($key=="item" || $key=="entry") continue;
            if($key=="items") {
                $rss1 = true; continue;
            }
            if(!$val->children()) {
                $feed[$key][0] = (string)$val;
                foreach($ns as $a=>$u) foreach($val->attributes($u) as $at=>$atv) {
                    $feed[$key][$at] = (string)$atv;
                }
            } else {
                foreach($val->children() as $k=>$v) {
                    $feed[$key][$k] = (string)$v;
                    foreach($v->attributes() as $at=>$atv) {
                        $feed[$k][$at] = (string)$atv;
                    }
                }
            }
        }

        // Now we deal with the items
        // Atom and RSS1 have the feed items a level higher than RSS2
        $i = 0;
        if($rss1) { $feed['_type']='rss1.0'; $items = $xml->item; }
        else if($atom10) { $feed['_type']='atom1.0'; $items = $xml->entry; }
        else if($atom03) { $feed['_type']='atom0.3'; $items = $xml->entry; }
        else {
            if($feed['version']=='2.0') $feed['_type']='rss2.0';
            else if($feed['version']=='0.91') $feed['_type']='rss0.91';
            $items = $xml->channel->item;
        }
        foreach($items as $key=>$val) {
            foreach($ns as $a=>$u) foreach($val->attributes($u) as $at=>$atv) {
                $feed['item'][$i][$at] = (string)$atv;
            }
            foreach($ns as $alias=>$uri) {
                foreach($val->children($uri) as $k=>$v) {
                    $feed['item'][$i][$k][0] = (string)$v;
                    foreach($v->attributes() as $at=>$atv) {
                        $at_val = (string)$atv;
                        if ($atom10 || $atom03) {
                            $feed['item'][$i][$k][$at][] = $at_val;
                            // Don't even try parsing this stuff, just pass it through.
                            if($at_val=='xhtml' || $at_val=='html' || $at_val=='text') {
                                $tags = $v->children();
                                $feed['item'][$i][$k]['text'] = $tags->asXML();
                            }
                        } else $feed['item'][$i][$k][$at] = $at_val;

                    }
                }
            }
            $i++;
        }
        return $feed;
    }
}

?>
