<?php
/**
 * Board.php
 *
 */

require_once dirname(__FILE__) . '/Common.php';

/**
 * Services_2chClient_Board
 *
 * @see http://age.s22.xrea.com/talk2ch/ 
 * @see http://info.2ch.net/wiki/index.php?subject.txt%A4%CE%BB%C5%CD%CD
 */
class Services_2chClient_Board extends Services_2chClient_Common
{
    /**
     * ホスト名・パス
     */
    private $_path;

    /**
     * 板キー
     */
    private $_directory;

    private $setting = array();

    private $subject;

    public function __construct($url, $board_key)
    {
        //$this->_path = 'gimpo.2ch.net';
        //$this->_directory = 'namazuplus';
        
        $this->_path = $url;
        $this->_directory = $board_key;
    }

    /**
     * fetchThreadList
     *
     * subjectとres数の配列を返す
     *
     * @todo 全体的にnaosu
     *
     */
    public function fetchThreadList()
    {
        $this->load();

        //$result = $this->export();

        return $this->subject;
    }

    /**
     * load
     *
     */
    public function load()
    {
        $url = 'http://'.$this->_path.'/'.$this->_directory.'/subject.txt';

        $httpObject =& new HTTP_Request($url);
        $httpObject->addHeader('User-Agent', $this->_userAgent);
        $httpObject->addHeader('Accept-Encoding', 'gzip');

        if($this->_lastModified){
            $httpObject->addHeader('If-Modified-Since', $this->_lastModified);
        }

        $response = $httpObject->sendRequest();

        if (PEAR::isError($response)) {
            return false;
        }

        $responseCode = $httpObject->getResponseCode();
        if ($responseCode != "200") {
            throw new Exception("Invalid response code:{$responseCode}");
        }

        $body = $httpObject->getResponseBody();
        $subjectText = preg_split("/\n/", $body);

        $this->subject = array();
        foreach ($subjectText as $line) {
            $result = preg_match('/^([\d]+)\.dat\<\>(.*) \(([\d]+)\)$/', $line, $match);
            if (!$result) {
                continue;
            }
            $this->subject[$match[1]] = array( 'subject' => $match[2],
                'res' => $match[3],);
        }

        //最終更新時刻を変更
        $this->_lastModified = $httpObject->getResponseHeader('Last-Modified');

        //レスポンスコードを返す。
        return $responseCode;
    }

    /**
     * export
     *
     * loadしたものを元に戻して返す？
     */
    public function export()
    {
        if (!$this->subject) {
            return false;
        }

        $subjectText;
        foreach ($this->subject as $key => $line) {
            $subjectText .= $key.'.dat<>'.$line['subject'].'('.$line['res'].")\r\n";
        }

        return $subjectText;
    }

    /**
     * loadSetting
     *
     */
    public function loadSetting(){
        $httpObject =& new HTTP_Request('http://'.$this->_path.'/'.$this->_directory.'/SETTING.TXT');
        $httpObject->addHeader('User-Agent', $this->_userAgent);
        $httpObject->addHeader('Accept-Encoding', 'gzip');

        $response = $httpObject->sendRequest();

        if (!PEAR::isError($response)) {
            return false;
        }
        $responseCode = $httpObject->getResponseCode();
        if ($responseCode == "200") {
            $this->subject = array();
            $this->parseSetting($request->getResponseBody());
        }
        //レスポンスコードを返す。
        return $responseCode;
    }

    function parseSetting($settingText){
        if ($settingText) {
            return false;
        }
        $settingText = preg_split("/\n/", $settingText);

        $this->setting = array();
        foreach ($settingText as $settingLine) {
            if (is_null($settingLine)) {
                continue;
            }
            $settingText = explode('=',$settingLine,2);
            $this->setting[$settingText[0]] = $settingText[1];
        }
        return true;
    }

    public function exportSetting(){
            if (!$this->subject) {
            return false;
        }

        $settingText;
        foreach ($this->setting as $key => $val) {
            $settingText .= $key.'='.$val."\r\n";
        }

        return $settingText;
    }

    /**
     * サーバ移転追尾
     *
     * @param int   $redirectMax    最大追尾回数
     */
    public function trackServer($redirectMax = 10)
    {
        if ($redirectMax <= 1) {
            $redirectMax = 1;
        } elseif ($redirectMax >= 100) {
            $redirectMax = 100;
        }
        $path = $this->_path;

        $i;
        for ($i=0; $i<=$redirectMax; $i++) {
            $httpObject  =& new HTTP_Request( 'http://'.$path.'/'.$this->_directory );
            $httpObject->addHeader('User-Agent', $this->_userAgent);
            $httpObject->addHeader('Accept-Encoding', 'gzip');

            if($this->_lastModified) {
                $httpObject->addHeader('If-Modified-Since', $this->_lastModified);
            }

            $response = $httpObject->sendRequest();
            if (!PEAR::isError($response)) {
                return false;
            }

            $html = $request->getResponseBody();
            $count = preg_match('/Change your bookmark ASAP\.\r?\n?\<a href=\"http:\/\/([\w\.\/\-~_]+)\/([\w]+)\/\"\>GO !\<\/a\>\r?\n?\<\/body\>/', $html, $match);
            if (!$count) {
                break;
            }
            $path = $match[1];
        }

        if ($i == 0) {
            return false;
        }else{
            $this->_path = $path;
            return true;
        }
    }
}
