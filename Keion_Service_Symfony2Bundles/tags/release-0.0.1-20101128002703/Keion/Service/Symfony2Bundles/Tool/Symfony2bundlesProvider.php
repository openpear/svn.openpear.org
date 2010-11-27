<?php

/**
 * Keion - Azunyan pero-pero Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Keion
 * @package    Keion_Service
 * @subpackage Symfony2Bundles
 * @copyright  2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

namespace Keion\Service\Symfony2Bundles\Tool;
require_once 'Keion/Service/Symfony2Bundles/Symfony2Bundles.php';

use \Zend_Tool_Framework_Provider_Abstract as AbstractProvider;

/**
 * Symfony2Bundles
 *
 * @see http://symfony2bundles.org/api
 */
class Symfony2bundlesProvider extends AbstractProvider
{
    protected $_service;

    public function getBundles($sort = 'name', $format = null)
    {
        $service = $this->_loadSymfony2Bundles();
        $response = $this->_registry->getResponse();
        $return = $service->getBundles($sort, $format);
        $response->appendContent(var_export($return, true));
    }

    public function getBundle($username, $name, $format = null)
    {
        $service = $this->_loadSymfony2Bundles();
        $response = $this->_registry->getResponse();
        $return = $service->getBundle($username, $name, $format);
        $response->appendContent(var_export($return, true));
    }

    public function getProjects($sort, $format = null)
    {
        $service = $this->_loadSymfony2Bundles();
        $response = $this->_registry->getResponse();
        $return = $service->getProjects($sort, $format);
        $response->appendContent(var_export($return, true));
    }

    public function getProject($username, $name, $format = null)
    {
        $service = $this->_loadSymfony2Bundles();
        $response = $this->_registry->getResponse();
        $return = $service->getProject($username, $name, $format);
        $response->appendContent(var_export($return, true));
    }

    public function search($query, $format = null)
    {
        $service = $this->_loadSymfony2Bundles();
        $response = $this->_registry->getResponse();
        $return = $service->search($query, $format);
        $response->appendContent(var_export($return, true));
    }

    public function getDevelopers($sort, $format = null)
    {
        $service = $this->_loadSymfony2Bundles();
        $response = $this->_registry->getResponse();
        $return = $service->getDevelopers($sort, $format);
        $response->appendContent(var_export($return, true));
    }

    public function getDeveloper($name, $format = null)
    {
        $service = $this->_loadSymfony2Bundles();
        $response = $this->_registry->getResponse();
        $return = $service->getDeveloper($name, $format);
        $response->appendContent(var_export($return, true));
    }

    public function getDeveloperBundles($name, $format = null)
    {
        $service = $this->_loadSymfony2Bundles();
        $response = $this->_registry->getResponse();
        $return = $service->getDeveloperBundles($name, $format);
        $response->appendContent(var_export($return, true));
    }

    public function getDeveloperProjects($name, $format = null)
    {
        $service = $this->_loadSymfony2Bundles();
        $response = $this->_registry->getResponse();
        $return = $service->getDeveloperProjects($name, $format);
        $response->appendContent(var_export($return, true));
    }

    protected function _loadSymfony2Bundles()
    {
        if (null === $this->_service) {
            $service = new \Keion\Service\Symfony2Bundles\Symfony2Bundles();
            if ($config = $this->_loadConfig('symfony2bundles')) {
                if ($config->httpClient) {
                    require_once 'Zend/Http/Client.php';
                    $client = new \Zend_Http_Client(null, $config->httpClient);
                    $service->setHttpClient($client);
                }
                $service->setConfig($config);
            }

            $this->_service = $service;
        }

        return $this->_service;
    }

    protected function _loadConfig($key)
    {
        $userConfig = $this->_registry->getConfig();

        return $userConfig->$key;
    }
}
