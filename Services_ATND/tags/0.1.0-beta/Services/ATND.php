<?php
require_once 'ATND/Exception.php';
class Services_ATND {
    const BASEURL = 'http://api.atnd.org/';
    /**
     * コンストラクタ
     */
    public function __construct() {
    }
    /**
     * イベントサーチ
     *
     * @param  array  $params
     * @param  string $format
     * @return mixed
     */
    public function events(array $params, $format = null) {
        return $this->_send('events', $params, $format);
    }

    /**
     * 出欠確認
     *
     * @param  array  $params
     * @param  string $format
     * @return mixed
     */
    public function users(array $params, $format = null) {
        return $this->_send('events/users', $params, $format);
    }
    /**
     * URLに接続します
     *
     * @param  string $method
     * @param  array  $params
     * @param  string $format
     * @return string|array|object
     */
    private function _send($method, $params = array(), $format = null) {
        $url = sprintf('%s%s/?%s',
                       self::BASEURL,
                       $method,
                       $this->_buildQuery($params, $format));
        if (!extension_loaded('curl')) {
            throw new Services_ATND_Exception('cURL extension not loaded.');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Services_ATND_Exception(' error:' . curl_error($ch), curl_errno($ch), $ch);
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
            throw new Services_ATND_Exception('error', curl_getinfo($ch, CURLINFO_HTTP_CODE), $ch);
        }
        curl_close($ch);
        if ($format === 'xml' || $format === 'atom') {
            return simplexml_load_string($response);
        }
        return $response;
    }
    /**
     * クエリーストリングを作成します
     *
     * @param array $params
     * @param string $format
     * @return string
     */
    private function _buildQuery($params = array(), $format = null) {
        if (!is_null($format)) {
            $params['format'] = $format;
        }
        $queries = array();
        foreach ($params as $key => $value) {
            if(!is_null($value)) {
//                $queries[] = $key . '=' . urlencode($value);
                $queries[] = $key . '=' . $value;
            }
        }
//        return implode('&amp;', $queries);
        return implode('&', $queries);
    }
}