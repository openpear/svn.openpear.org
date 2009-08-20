<?php
/**
 * レコメンドエンジンCicindelaのWebAPIラッパ
 *
 * PHP version 5
 * 
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt. If you did not receive a copy
 * the PHP License and are unable to obtain it through the web,
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category Net
 * @package Net_Cicindela
 * @author TANAKA Koichi <tanaka@ensites.com>
 * @copyright authors
 * @license http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version $Id$
 * @link http://d.hatena.ne.jp/Mugeso/
 * @see http://code.google.com/p/cicindela2/
 */
require_once 'HTTP/Request2.php';
require_once 'Net/URL2.php';
require_once 'Cicindela/Dataset.php';

class Net_Cicindela
{
    const VERSION = '0.2.1';
    private $baseUrl;

    /**
     *
     * @var HTTP_Request2
     */
    private $request;

    public function __construct($baseurl = 'http://localhost/cicindela/', $request = null)
    {
        $this->baseUrl = $baseurl;
        $this->request = $request instanceof HTTP_Request2 ? $request : new HTTP_Request2();
        $this->request->setHeader('User-Agent', 'Net_Cicindela/' . self::VERSION .
            ' (http://openpear.org/package/Net_Cicindela) ' .
            'PHP/' . phpversion());
    }

    /**
     * データセットを取得する
     * 
     * @param string $name データセット名
     * @return Net_Cicindela_Dataset
     */
    public function getDataset($name)
    {
        return new Net_Cicindela_Dataset($name, $this);
    }

    /**
     * ベースURLを取得する
     * 
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * 情報を記録する
     *
     * 通常はNet_Cicindela_Datasetから呼び出されるので
     * 普通の人は気にしなくてよい。
     * 
     * @param array $param
     */
    public function record(array $param)
    {
        $url = new Net_URL2($this->getBaseUrl() . 'record');
        $url->setOption(Net_URL2::OPTION_SEPARATOR_OUTPUT, '&'); // arg_separator.outputに左右されては困る。
        $url->setQueryVariables($param);
        $request = $this->request;
        $request->setURL($url);
        $response = $request->send();

        $responseCode = $response->getStatus();
        if($responseCode!==204) {
            throw new RuntimeException('Bad response returned.', $responseCode);
        }
    }

    /**
     * レコメンドを取得する
     *
     * 通常はNet_cicindela_Datasetから呼び出されるので、
     * 普通の人は気にしなくてよい。
     *
     * @param array $param
     * @return array
     */
    public function getRecommend(array $param)
    {
        $url = new Net_URL2($this->getBaseUrl() . 'recommend');
        $url->setOption(Net_URL2::OPTION_SEPARATOR_OUTPUT, '&'); // arg_separator.outputに左右されては困る。
        $url->setQueryVariables($param);
        $request = $this->request;
        $request->setURL($url);
        $response = $request->send();

        $responseCode = $response->getStatus();
        if($responseCode !== 200) {
            throw new RuntimeException('Bad response returned.', $responseCode);
        }

        return array_filter(explode("\n", $response->getBody()));
    }
}
?>
