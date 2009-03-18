<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Text dependency parsingclass
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the New BSD license that is
 * available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/bsd-license.php. If you did not receive
 * a copy of the New BSD License and are unable to obtain it through the web,
 * please send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Services
 * @package   Services_Yahoo_JP_Text
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki Shimooka
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: $Id: dependency.php,v 1.1 2008/04/28 15:59:30 tetsuya Exp $
 */

require_once 'AbstractText.php';

/**
 * Services_Yahoo_JP_Text_Dependency
 *
 * This class implements an interface to Yahoo! JAPAN's Cateogry Tree by using
 * the Yahoo API.
 *
 * @category  Services
 * @package   Services_Yahoo_JP_Text
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki Shimooka
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   Release: 0.0.1
 */
class Services_Yahoo_JP_Text_Dependency extends Services_Yahoo_JP_Text_AbstractText
{
    /**
     * api url
     *
     * @access protected
     * @var    string
     */
    protected $requestURL = 'http://jlp.yahooapis.jp/DAService/V1/parse';
}
?>
