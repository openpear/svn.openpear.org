<?php
/**
 * BoardList.php
 *
 * @package Services_2chClient
 */

require_once dirname(__FILE__) . '/Common.php';

/**
 * Services_2chClient_BoardList
 *
 */
class Services_2chClient_BoardList extends Services_2chClient_Common
{
    /**
     * 板一覧のURL
     */
    private $_url = 'http://menu.2ch.net/bbsmenu.html';

    private $boardlist = array();

    function load(){
        $httpObject =& new HTTP_Request($this->url);
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
            //200(取得成功)の場合は処理続行
            $this->importFromHtml('bbsmenu', $request->getResponseBody());
            //最終更新時刻を変更
            $this->_lastModified($httpObject->getResponseHeader('Last-Modified'));
        }
        //レスポンスコードを返す。
        return $responseCode;
    }

    /**
     * 板リスト読み込み
     *
     * @param   string  $boardlistText  板リスト
     * @param   string  $type           板リストタイプ
     */
    function import($boardlistText, $type = 'bbsmenu'){
        if(empty($boardlistText)){
            return false;
        }
        $boardlistText = preg_split("/\n/", $boardlistText);
        $categoryId = -1;

        switch($type){
            case 'bbsmenu':
                do {
                    $line = array_shift($boardlistText);
                    $result = preg_match('/\<B\>(.+)\<\/B\>/', $line, $match);
                } while (!$result);
                $this->boardlist[++$categoryId] = array ( 'category' => $match[1],
                                                          'option' => '');
                foreach ($boardlistText as $line) {
                    $result = preg_match('/\<B\>(.+)\<\/B\>/', $line, $match);
                    if ($result) {
                        $this->boardlist[++$categoryId] = array ( 'category' => $match[1],
                                                                  'option' => '',
                                                                  'boards' => array() );
                        continue;
                    }
                    $result = preg_match('/\<A HREF=http:\/\/([\w\.\/\-~_]+)\/([\w]+)\/(index2.html?)?\>(.+)\<\/A\>/', $line, $match);
                    if ($result) {
                        $this->boardlist[$categoryId]['boards'][] = array ( 'base' => $match[1],
                                                                            'directory' => $match[2],
                                                                            'name' => $match[4]);
                    }
                }
                break;

            case 'bbstable':
                do {
                    $line = array_shift($boardlistText);
                    $result = preg_match('/【\<B\>(.+)\<\/B\>】/', $line, $match);
                } while (!$result);
                $this->boardlist[++$categoryId] = array ( 'category' => $match[1],
                                                          'option' => '');
                foreach ($boardlistText as $line) {
                    $result = preg_match('/【\<B\>(.+)\<\/B\>】/', $line, $match);
                    if ($result) {
                        $this->boardlist[++$categoryId] = array ( 'category' => $match[1],
                                                                  'option' => '',
                                                                  'boards' => array() );
                    }
                    $result = preg_match('/\<A HREF=http:\/\/([\w\.\/\-~_]+)\/([\w]+)\/(index2.html?)?\>(.+)\<\/A\>/', $line, $match);
                    if ($result) {
                        $this->boardlist[$categoryId]['boards'][] = array ( 'base' => $match[1],
                                                                            'directory' => $match[2],
                                                                            'name' => $match[4]);
                    }
                }
                break;

            case 'brd';
                foreach ($boardlistText as $boardlistLine) {
                    $lineParts = preg_split('/\t/', $boardlistLine);
                    switch (count($lineParts)) {
                        case 2:
                            $this->boardlist[++$categoryId] = array ( 'category' => $lineParts[0],
                                                                      'option' => trim($lineParts[1]) );
                        break;

                        case 4:
                            $this->boardlist[$categoryId]['boards'][] = array( 'base' => $lineParts[1],
                                                                               'directory' => $lineParts[2],
                                                                               'name' => trim($lineParts[3]) );
                    }
                }
                break;

            default:
                return false;
        }
        return true;
    }

    /**
     * かちゅ～しゃ・Jane形式の板リストを返す
     *
     * @return String かちゅ～しゃ・Jane形式の板リスト
     */
    function export(){
        if(!$this->boardlist){
            return null;
        }
        //互換のため、先頭行にゼロを挿入
        $boardlistText = "0\r\n";
        foreach ($this->boardlist as $category) {
            $boardlistText .= $category['category'] . "\t" . $category['option'] . "\r\n";
            foreach ($category['boards'] as $board) {
                $boardlistText .= "\t" . $board['base'] .
                                  "\t" . $board['directory'] .
                                  "\t" . $board['name'] . "\r\n";
            }
        }
        return $boardlistText;
    }
}
