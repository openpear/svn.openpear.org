<?php

final class Diggin_Http_Response_Charset
{
    final private function __construct()
    {}

    final public static function wrapResponse($response, $url = null)
    {
        if ($response instanceof Zend_Http_Response) {

            $headers = $response->getHeaders();
            if (isset($headers['Content-type'])) {
                $headers['Content-type'] = trim(preg_replace('/charset=[A-Za-z0-9-_]+;*/i', '', $headers['Content-type']));
            }
            
            require_once 'Diggin/Http/Response/Charset/Wrapper/Zf.php';
            $response = new Diggin_Http_Response_Charset_Wrapper_Zf($response->getStatus(), 
                                                $headers,
                                                $response->getRawBody(),
                                                $response->getVersion(),
                                                $response->getMessage());
            $response->setUrl($url);

            return $response;
        } else {
            require_once 'Diggin/Http/Response/Charset/Exception.php';
            throw new Diggin_Http_Response_Charset_Exception('Unknown Object Type..');
        }
    }
}
