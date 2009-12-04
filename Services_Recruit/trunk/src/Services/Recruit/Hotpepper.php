<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
* @category  Web Services
* @package   Services_Recruit_Hotpepper
* @author    Tadashi Jokagi <elf@php.net>
* @copyright 2008 John Downey
* @license   http://www.freebsd.org/copyright/freebsd-license.html 2 Clause BSD License
* @version   CVS: $Id$
* @link      http://pear.php.net/package/Services_Recruit_Hotpepper/
* @filesource
*/

require_once 'Services/Recruit/common.php';

/**
* Class for accessing and retrieving information from Hotpepper's Web Services.
*
* @package Services_Recruit_Hotpepper
* @author  Tadashi Jokagi <elf@php.net>
* @access  public
* @version Alpha: 0.1.0
* @uses    PEAR
* @uses    Services_Recruit_common
* @uses    Services_Recruit
*/
class Services_Recruit_Hotpepper extends Services_Recruit_common
{
    function setServiceApiVersion($version = null)
    {
        if (is_null($version)) {
            parent::setServiceApiVersion('1');
        } else {
            parent::setServiceApiVersion($version);
        }
    }

    function searchGourmet($params, $start = null, $count = null)
    {
        return $this->_sendRequest('gourmet', $params, $start, $count);
    }

    function searchShop($params, $start = null, $count = null)
    {
        return $this->_sendRequest('shop', $params, $start, $count);
    }

    function getBudget($start = null, $count = null)
    {
        return $this->_sendRequest('budget', array(), $start, $count);
    }

    function getServiceAreaLarge($start = null, $count = null)
    {
        return $this->_sendRequest('large_service_area', array(), $start, $count);
    }

    function getServiceArea($start = null, $count = null)
    {
        return $this->_sendRequest('service_area', array(), $start, $count);
    }

    function searchAreaLarge($params, $start = null, $count = null)
    {
        return $this->_sendRequest('large_area', $params, $start, $count);
    }

    function searchAreaMiddle($params, $start = null, $count = null)
    {
        return $this->_sendRequest('middle_service_area', $params, $start, $count);
    }

    function searchAreaSmall($params, $start = null, $count = null)
    {
        return $this->_sendRequest('small_service_area', $params, $start, $count);
    }

    function searchGenre($params, $start = null, $count = null)
    {
        return $this->_sendRequest('genre', $params, $start, $count);
    }

    function searchFood($params, $start = null, $count = null)
    {
        return $this->_sendRequest('food', $params, $start, $count);
    }
}

?>
