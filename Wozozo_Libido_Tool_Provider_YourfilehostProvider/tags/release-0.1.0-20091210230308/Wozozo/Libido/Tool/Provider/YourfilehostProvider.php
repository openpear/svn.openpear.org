<?php

require_once 'Zend/Http/Client/Adapter/Socket.php';
class Wozozo_Libido_Tool_Provider_YourfilehostProvider_HttpClientAdapterSocketProgressBar
        extends Zend_Http_Client_Adapter_Socket
{
    private $_max = 100;
    private $_progressBar;

    private function setMax($max)
    {
        $this->_max = $max;
    }

    private function getMax()
    {
        return $this->_max;
    }

    private function getProgressBar()
    {
        if (PHP_SAPI != 'cli') throw new RuntimeException();

        if (!$this->_progressBar) {
            require_once 'Zend/ProgressBar/Adapter/Console.php';
            require_once 'Zend/ProgressBar.php';
            $adapter = new Zend_ProgressBar_Adapter_Console();
            $this->_progressBar = new Zend_ProgressBar($adapter, 0, $this->getMax());
        }
        
        return $this->_progressBar;

    }

    /**
     * borrowed from Zend_Http_Client_Adapter_Socket
     * Zend Framework is new BSD LICENSE.
     * (@see http://framework.zend.com/license/new-bsd)
     *
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        // First, read headers only
        $response = '';
        $gotStatus = false;

        while (($line = @fgets($this->socket)) !== false) {
            $gotStatus = $gotStatus || (strpos($line, 'HTTP') !== false);
            if ($gotStatus) {
                $response .= $line;
                if (rtrim($line) === '') break;
            }
        }

        $this->_checkSocketReadTimeout();

        $statusCode = Zend_Http_Response::extractCode($response);

        // Handle 100 and 101 responses internally by restarting the read again
        if ($statusCode == 100 || $statusCode == 101) return $this->read();

        // Check headers to see what kind of connection / transfer encoding we have
        $headers = Zend_Http_Response::extractHeaders($response);

        /**
         * Responses to HEAD requests and 204 or 304 responses are not expected
         * to have a body - stop reading here
         */
        if ($statusCode == 304 || $statusCode == 204 ||
            $this->method == Zend_Http_Client::HEAD) {

            // Close the connection if requested to do so by the server
            if (isset($headers['connection']) && $headers['connection'] == 'close') {
                $this->close();
            }
            return $response;
        }

        // If we got a 'transfer-encoding: chunked' header
        if (isset($headers['transfer-encoding'])) {

            if (strtolower($headers['transfer-encoding']) == 'chunked') {

                do {
                    $line  = @fgets($this->socket);
                    $this->_checkSocketReadTimeout();

                    $chunk = $line;

                    // Figure out the next chunk size
                    $chunksize = trim($line);
                    if (! ctype_xdigit($chunksize)) {
                        $this->close();
                        require_once 'Zend/Http/Client/Adapter/Exception.php';
                        throw new Zend_Http_Client_Adapter_Exception('Invalid chunk size "' .
                            $chunksize . '" unable to read chunked body');
                    }

                    // Convert the hexadecimal value to plain integer
                    $chunksize = hexdec($chunksize);

                    // Read next chunk
                    $read_to = ftell($this->socket) + $chunksize;

                    do {
                        $current_pos = ftell($this->socket);
                        if ($current_pos >= $read_to) break;

                        $line = @fread($this->socket, $read_to - $current_pos);
                        if ($line === false || strlen($line) === 0) {
                            $this->_checkSocketReadTimeout();
                            break;
                        } else {
                            $chunk .= $line;
                        }

                    } while (! feof($this->socket));

                    $chunk .= @fgets($this->socket);
                    $this->_checkSocketReadTimeout();

                    $response .= $chunk;
                } while ($chunksize > 0);

            } else {
                $this->close();
                throw new Zend_Http_Client_Adapter_Exception('Cannot handle "' .
                    $headers['transfer-encoding'] . '" transfer encoding');
            }

        // Else, if we got the content-length header, read this number of bytes
        } elseif (isset($headers['content-length'])) {

            $this->setMax($headers['content-length']);

            /** start  Zend_ProgressBar **/
            echo 'Content-length:', $headers['content-length']. PHP_EOL;
            $this->getProgressBar();

            $current_pos = ftell($this->socket);
            $chunk = '';

            for ($read_to = $current_pos + $headers['content-length'];
                 $read_to > $current_pos;
                 $current_pos = ftell($this->socket)) {

                $this->getProgressBar()->update($current_pos);

                $chunk = @fread($this->socket, $read_to - $current_pos);
                if ($chunk === false || strlen($chunk) === 0) {
                    $this->_checkSocketReadTimeout();
                    break;
                }

                $response .= $chunk;

                // Break if the connection ended prematurely
                if (feof($this->socket)) break;
            }

            $this->getProgressBar()->finish();

        // Fallback: just read the response until EOF
        } else {

            do {
                $buff = @fread($this->socket, 8192);
                if ($buff === false || strlen($buff) === 0) {
                    $this->_checkSocketReadTimeout();
                    break;
                } else {
                    $response .= $buff;
                }

            } while (feof($this->socket) === false);

            $this->close();
        }

        // Close the connection if requested to do so by the server
        if (isset($headers['connection']) && $headers['connection'] == 'close') {
            $this->close();
        }

        return $response;
    }

}

require_once "Zend/Tool/Framework/Provider/Abstract.php";

class Wozozo_Libido_Tool_Provider_YourfilehostProvider extends Zend_Tool_Framework_Provider_Abstract
{
    const CONFIG_NULL = 'null';

    private $httpClient = null;

    protected function setHttpClientConfigFromJson($json)
    {
        require_once 'Zend/Json.php';
        if($config = Zend_Json::decode($json, Zend_Json::TYPE_ARRAY)) {
            $this->getHttpClient()->setConfig($config);
        } else {
            throw new InvalidArgumentException("'$json' is not valid json");
        }
    }

    /**
     * @return Zend_Http_Client
     */
    protected function getHttpClient()
    {
        if($this->httpClient == null) {
            require_once 'Zend/Http/Client.php';
            $config = array('useragent' => 'Wozozo_Libido');
            $this->httpClient = new Zend_Http_Client(null, $config);
        }
        return $this->httpClient;
    }

    private function scrape($url)
    {
        require_once 'Diggin/Scraper.php';

        Diggin_Scraper::setHttpClient($this->getHttpClient());

        if (!extension_loaded('tidy')) {
            require_once 'Diggin/Scraper/Adapter/Loadhtml.php';
            Diggin_Scraper::changeStrategy('Diggin_Scraper_Strategy_Flexible', new Diggin_Scraper_Adapter_Loadhtml());
        }

        $scraper = new Diggin_Scraper();
        $ret = $scraper->process('//param[@name="movie"]', "value => @value")
                       ->scrape($url);

        parse_str(Zend_Uri::factory($ret['value'])->getQuery(), $ret);
        $this->getHttpClient()->setUri($ret['video']);
        parse_str($this->getHttpClient()->request()->getBody(), $ret);

        return $ret;
    }

    public function download($url, $dir = '.', $httpConfig = self::CONFIG_NULL) 
    {
        if ($httpConfig != self::CONFIG_NULL) $this->setHttpClientConfigFromJson($httpConfig);

        $ret = $this->scrape($url);

        //if (array_key_exists('video_id', $ret))
        //

        $client = $this->getHttpClient();
        $client->setAdapter(new Wozozo_Libido_Tool_Provider_YourfilehostProvider_HttpClientAdapterSocketProgressBar());
        $videoUri = Zend_Uri::factory($ret['video_id']);
        $client->setUri($videoUri);

        // @todo  dir Check
        $dir = ($dir == '.') ? $_SERVER['PWD']: $dir;
        // filename is named by query file
        parse_str(Zend_Uri::factory($url)->getQuery(), $query);
        $filepath = $query['file'];
        $path = realpath($dir).'/'.$filepath.'.flv';

        echo "download to $path", PHP_EOL;

        $response = $client->request();

        file_put_contents($path, $response->getRawBody());
    }


    private function test($config)
    {
        $this->setHttpClientConfigFromJson($config);
        //$this->_registry->getResponse()->appendContent(Wozozo_Libido_Tool_Provider_YourfilehostProvider_HttpClientAdapterSocketProgressBar::TEST);
    }

}


