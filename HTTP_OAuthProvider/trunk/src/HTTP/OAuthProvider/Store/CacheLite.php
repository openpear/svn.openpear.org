<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OAuth authentication class for service provider.
 *
 * PHP versions 5
 *
 * @category  HTTP
 * @package   OAuthProvider
 * @author    Tetsuya Yoshida <tetu@eth0.jp>
 * @copyright 2010 Tetsuya Yoshida
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   1.0.9
 * @link      http://openpear.org/package/HTTP_OAuthProvider
 */
require_once 'Cache/Lite.php';

/**
 * OAuth token store class for service provider.
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.0.9
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_Store_CacheLite extends HTTP_OAuthProvider_Store
{
    protected $options = array(
        'cacheDir' => '/tmp/http_oauthprovider/',
        'lifeTime' => 3600
    );
    protected $cache = null;


    /**
     * __construct
     * 
     * Generate the HTTP_OAuthProvider_Store_CacheLite instance.
     * 
     * @param Array $options Store options.
     * 
     * @return HTTP_OAuthProvider_Store_CacheLite
     * 
     * @throws HTTP_OAuthProvider_Store_Exception If failing in the make directory.
     */
    public function __construct(array $options=array())
    {
        $this->options = array_merge($this->options, $options);
        $this->options['cacheDir'] = rtrim($this->options['cacheDir'], '/').'/';
        $dir = $this->options['cacheDir'];
        // make cache dir
        if (!is_dir($dir)) {
            $maked = @mkdir($dir, 0777, true);
            if (!$maked) {
                $message = sprintf("Can's make directory: %s", $dir);
                throw new HTTP_OAuthProvider_Store_Exception($message, 500);
            }
        }
        // make store instance
        $this->cache = new Cache_Lite($this->options);
    }

    /**
     * get
     * 
     * Retrieve a token
     * 
     * @param String $token A token to retrive.
     * 
     * @return Array
     */
    public function get($token)
    {
        return unserialize($this->cache->get($token));
    }

    /**
     * save
     * 
     * Save a token
     * 
     * @return Boolean
     */
    public function save()
    {
        return $this->cache->save(serialize($this->row), $this->getToken());
    }

    /**
     * remove
     * 
     * Remove a token
     * 
     * @return Boolean
     */
    public function remove()
    {
        return $this->cache->remove($this->getToken());
    }
}
