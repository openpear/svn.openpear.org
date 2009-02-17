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
  * @author     KOYAMA Tetsuji <koyama@hoge.hoge.org>
  * @copyright  2008-2009 KOYAMA Tetsuji
  * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
  * @version    svn: $Id$
  * @link       http://openpear.org/package/Keires_Feed
  * @since      File available since Release 0.1
  */

require_once 'Keires/FeedParser.php';
require_once 'Keires/ExceptionWrapper.php';
require_once 'HTTP/Request.php';  // PEAR::HTTP_Request

/*
 * Error codes
 */
define('KEIRES_ERROR_INVALID_PARAM',   1);
define('KEIRES_ERROR_OPEN_FILE',       2);
define('KEIRES_ERROR_EMPTY_CONTENTS',  3);
define('KEIRES_ERROR_UNPARSED',        4);
define('KEIRES_ERROR_INVALID_XML',     5);
define('KEIRES_ERROR_DOM_LOADING',     6);

/*
 * Utility function
 */
if (!function_exists('array_val')) {
    function array_val(&$data, $key, $default = null) {
        if (!is_array($data)) {
            return $default;
        }
        return isset($data[$key])? $data[$key]: $default;
    }
}

/*
 * Excepiton
 */
class Keires_Exception extends Exception {
    // empty
}

class Keires_Feed {
    protected $contents = null;
    protected $parser = null;
    
    public function __construct($url, $options = null) {
        $noreq = array_val($options, 'noreq', false);
        $ua = array_val($options, 'user_agent');

        if ($noreq) {
            return;
        }
        
        if (empty($url)) {
            throw new Keires_Exception('url is required',
                                       KEIRES_ERROR_INVALID_PARAM);
        }

        $http_opts = array(
            'allowRedirects' => true, 
            );
        $req = new HTTP_Request($url, $http_opts);
        if (!empty($ua)) {
            $req->addHeader('User-Agent', $ua);
        }
        $result = $req->sendRequest();
        if (PEAR::isError($result)) {
            Keires_ExceptionWrapper::handleError($result);
        }

        $code = $req->getResponseCode();
        if ($code != 200) {
            throw new Keires_Exception('path open failed: '. $url,
                                       KEIRES_ERROR_OPEN_FILE);
        }

        $cont = $req->getResponseBody();
        if ($cont === false) {
            throw new Keires_Exception('path open failed: '. $url,
                                       KEIRES_ERROR_OPEN_FILE);
        }

        $this->contents = $cont;
    }

    public function setContents($contents) {
        $this->contents = $contents;
    }

    public function getContents() {
        return $this->contents;
    }

    public function parse($options = null) {
        if (empty($this->contents)) {
            throw new Keires_Exception('empty contents',
                                       KEIRES_ERROR_EMPTY_CONTENTS);
        }
        $parser = new Keires_FeedParser($this->contents, $options);
        $parser->parse();

        $this->parser = $parser;
    }

    public function getItems() {
        if (empty($this->parser)) {
            throw new Keires_Exception('Not parsed yet',
                                       KEIRES_ERROR_UNPARSED);
        }
        $type = $this->parser->type();
        $item = $this->parser->items();
        return new Keires_FeedItem($item, $type);
    }
}

class Keires_FeedItem implements Iterator {
    protected $pos = 0;
    protected $type = null;
    protected $items = null;

    public function __construct($items, $type) {
        if (empty($items) || empty($type)) {
            throw new Keires_Exception('Empty params',
                                       KEIRES_ERROR_INVALID_PARAM);
        }
        $this->type = $type;
        $this->items = $items;
    }

    static protected function getVal($item, $key) {
        $v = array_val($item, $key);
        for (;;) {
            if (is_array($v)) {
                $v = $v[0];
            } else {
                break;
            }
        }
        return $v;
    }

    static public function getValKeys($item, $keys) {
        if (!is_array($keys)) {
            return null;
        }
        foreach ($keys as $key) {
            $data = self::getVal($item, $key);
            if (!empty($data)) {
                return $data;
            }
        }
        return null;
    }

    static public function parseEntryRSS($item) {
        $datekeys = array('date', 'pubDate');
        $pubdate = self::getValKeys($item, $datekeys);
        $entry = array(
            'url'         => self::getVal($item, 'link'),
            'title'       => self::getVal($item, 'title'),
            'description' => self::getVal($item, 'description'),
            'content'     => self::getVal($item, 'encoded'),
            'creator'     => self::getVal($item, 'creator'),
            'pubdate'     => $pubdate,
            );
        return $entry;
    }

    static public function parseEntryATOM($item) {
        // get url
        $link = $item['link'];
        $url = null;

        // for feedburner
        $url = self::getVal($item, 'origLink');

        if (empty($url)) {
            // first: find <link rel="alternate">
            foreach ($link['rel'] as $idx => $rel) {
                if ($rel === 'alternate') {
                    $url = $link['href'][$idx];
                    break;
                }
            }
        }
        if (empty($url)) {
            // second: <link type="text/html">
            foreach ($link['type'] as $idx => $type) {
                if (($type === 'text/html') ||
                    ($type === 'application/xhtml+xml')){
                    $url = $link['href'][$idx];
                    break;
                }
            }
        }
        if (empty($url)) {
            // for lifehacking.jp
            $url = self::getVal($item, 'id');
        }
        // get published date
        $datekeys = array(
            'created',
            'published',
            'modified',
            'issued',
            );
        $pubdate = self::getValKeys($item, $datekeys);
        $entry = array(
            'url'         => $url,
            'title'       => self::getVal($item, 'title'),
            'description' => self::getVal($item, 'summary'),
            'creator'     => self::getVal($item, 'author'),
            'pubdate'     => $pubdate,
            );
        return $entry;
    }

    public function rewind() {
        $this->pos = 0;
    }
    
    public function current() {
        $item = $this->items[$this->pos];
        $entry = null;
        if (strncmp($this->type, 'rss', 3) === 0) {
            $entry = self::parseEntryRSS($item);
        } else if (strncmp($this->type, 'atom', 4) === 0) {
            $entry = self::parseEntryATOM($item);
        }
        return $entry;
    }
    
    public function key() {
        return $this->pos;
    }
    
    public function next() {
        ++$this->pos;
    }

    public function valid() {
        return ($this->pos < count($this->items));
    }
}

?>
