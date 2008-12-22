<?php
/**
 * Board.php
 *
 */

require_once dirname(__FILE__) . '/Common.php';

/**
 * Services_2chClient_Board
 *
 */
class Services_2chClient_Board extends Services_2chClient_Common
{
    /**
     * ホスト名・パス
     */
    private $_path;
    /**
     * ディレクトリ名
     */
    private $_directory;
    private $setting = array();

    private $subject;

    function load(){
        $httpObject =& new HTTP_Request('http://'.$this->_path.'/'.$this->_directory.'/subject.txt');
        $httpObject->addHeader('User-Agent', $this->_userAgent);
        $httpObject->addHeader('Accept-Encoding', 'gzip');

        if($this->_lastModified){
            $httpObject->addHeader('If-Modified-Since', $this->_lastModified);
        }

        $response = $httpObject->sendRequest();

        if (!PEAR::isError($response)) {
            return false;
        }
        $responseCode = $httpObject->getResponseCode();
        if ($responseCode == "200") {
            $this->subject = array();
            $subjectText = preg_split("/\n/", $request->getResponseBody());

            foreach ($subjectText as $line) {
                $result = preg_match('/^([\d]+)\.dat\<\>(.*) \(([\d]+)\)$/', $line, $match);
                if (!$result) {
                    continue;
                }
                $this->subject[$match[1]] = array( 'subject' => $match[2],
                                                  'res' => $match[3],);
            }
            //最終更新時刻を変更
            $this->_lastModified($httpObject->getResponseHeader('Last-Modified'));
        }
        //レスポンスコードを返す。
        return $responseCode;
    }

    function export(){
        if (!$this->subject) {
            return false;
        }

        $subjectText;
        foreach ($this->subject as $key => $line) {
            $subjectText .= $key.'.dat<>'.$line['subject'].'('.$line['res'].")\r\n";
        }

        return $subjectText;
    }

    function loadSetting(){
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

    function exportSetting(){
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
    function trackServer($redirectMax = 10){
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
