<?php

require_once 'WikiHub/Exception.php';
require_once 'HatenaSyntax.php';

class WikiHub {

    const VERSION = "1.0.0";
    const URL     = "http://openpear.org/package/WikiHub";

    protected $_dir;

    public $browser = 'w3m';
    public $browser_option = array(
        'w3m' => '-T "text/html"',
    );

    public function __construct($dir, $browser = "w3m")
    {
        $this->_dir = $dir;

        exec('which ' . $browser, $output, $status);
        if ($status) {
            throw new WikiHub_Exception("$browser does not installed!\n");
        }

        $this->browser = $browser;
        $this->browser_cmd = trim($output[0]);
    }

    protected function getPath($path)
    {
        $path = ltrim($path, "/");
        return $this->_dir . "/" . $path;
    }

    public static function getHeader()
    {
        return '<h1>Wikihub :: preview</h1>
            <p> (This is only a preview, it is little different from web view. Inner link doe\'s not work.)</p><hr />';
    }

    public static function getFooter()
    {
        return '<hr /><em> -- powererd by nequal.</em>';
    }

    public static function runCommand($args)
    {
        $o = new self(getcwd());
        $path = $o->getPath($args);
        if (!file_exists($path)) {
            throw new WikiHub_Exception('file is not exists.');
        }

        $txt = file_get_contents($path);

        $html = self::getHeader();
        $html .=  HatenaSyntax::render($o->getTocIfSectionsCount(3, $txt) . $txt, array(
                'headerlevel' => 3,
                'htmlescape' => true,
                'keywordlinkhandler' => array($o, 'keywordLinkHandler'),
            )
        );
        $html .= self::getFooter();

        $w = popen(join(" ", array($o->browser_cmd, $o->browser_option[$o->browser],)), "w");
        fwrite($w, $html);
        pclose($w);
    }

    public function getTocIfSectionsCount($count, $txt)
    {
        $c = preg_match_all('!\n\*+?!', "\n" . $txt, $m);
        if ($c >= $count) {
            return "[:contents]\n";
        }
        return "";
    }

    public function keywordlinkhandler($path)
    {
        return $path;
    }

    public static function getUsage()
    {
        $version = self::VERSION;
        $url     = self::URL;

        return <<<EEE
usage: whprev <path>

example:
    % whprev hoge.html
    % whprev hoge/fuga.html

whprev - WikiHub preview command.
    version     $version
    url         $url
    author      Sotaro Karasawa

EEE;
    }
}
