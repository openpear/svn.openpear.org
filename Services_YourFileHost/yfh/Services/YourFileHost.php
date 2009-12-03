<?php
class Services_YourFileHost {
    /**
     * 動画URL
     * @var string
     */
    private $_url;

    /**
     * 動画URLのクエリパラム
     * @var string
     */
    private $_params;

    /**
     * 裏APIのクエリパラム
     * @var string
     */
    private $_query;

    /**
     * コンストラクタ
     * @param string $url
     */
    public function __construct($url = null) {
        if ($url === null) throw new Exception;
        if (!$this->_varidateUrl($url)) throw new Exception;
        $this->_url = $url;
        $this->_connect($url);
    }
    /**
     * マジックメソッド
     *
     * @param string $name
     * @return 裏APIのvalue(video_id/photo) 存在しない場合はnullを返す
     */
    public function __get($name) {
        if (array_key_exists($name, $this->_query)) {
            return urldecode($this->_query[$name]);
        }
        return null;
    }
    /**
     * YourFileHostのparam要素をスクレイピングして裏APIへ接続します
     * 
     * @param  動画URL $url
     * @return void
     */
    private function _connect($url) {
        $html = file_get_contents($url);
        $html = preg_replace('/iso-8859-1/i', 'UTF-8', $html);
        $html = mb_convert_encoding($html, 'UTF-8', 'iso-8859-1');

        $doc = new DOMDocument;
        @$doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $tags = $xpath->query('//param[@name="movie"]');
        if ($tags->length === 0) {
            return;
        }
        $tag = $tags->item(0);
        $value = parse_url($tag->getAttribute('value'));
        $params = $this->_setParams(explode('&', $value['query']));
        $this->_query = $this->_setParams(explode('&', file_get_contents(urldecode($params['video']))));
    }
    /**
     * YourFileHostのURLチェック
     *
     * @param  動画URL $url
     * @return boolean 正しいURLの場合はtrue 正しくない場合は false を返します
     */
    private function _varidateUrl($url) {
        $hash = parse_url($url);
        if (!$hash) return false;
        if ($hash['host'] !== 'www.yourfilehost.com' ||
                $hash['path'] !== '/media.php') {
            return false;
        }
        $this->_params = $this->_setParams(explode('&', $hash['query']));
        if (!array_key_exists('file', $this->_params)) {
            return false;
        }
        return true;
    }
    /**
     * 動画URLのパラメータをパースして変数にセットします
     *
     * @param  クエリパラム $params
     * @return パース結果
     */
    private function _setParams($params) {
        $q = array();
        foreach ($params as $param) {
            list($key, $value) = explode('=', $param);
            $q[$key] = $value;
        }
        return $q;
    }
}