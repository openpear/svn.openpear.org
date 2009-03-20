<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Abstract Furigana class
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
 * @package   Services_Yahoo_JP_Furigana
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki Shimooka
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: $Id: AbstractFurigana.php,v 1.1 2008/04/28 15:59:30 tetsuya Exp $
 */

require_once 'Services/Yahoo/JP/Furigana/Response.php';
require_once 'HTTP/Request.php';

/**
 * Abstract Furigana class
 *
 * This abstract class serves as the base class for all different
 * types of Furigana that available through Services_Yahoo.
 *
 * @category  Services
 * @package   Services_Yahoo_JP_Furigana
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki Shimooka
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   Release: 0.0.1
 */
abstract class Services_Yahoo_JP_Furigana_AbstractFurigana
{
    /**
     * parameter
     *
     * @access protected
     * @var    array
     */
    protected $parameters = array('appid' => 'PEAR_Services_Y_JP_Furigana');

    /**
     * Submits the Furigana
     *
     * This method submits the Furigana and handles the response.  It
     * returns an instance of Services_Yahoo_Result which may be used
     * to further make use of the result.
     *
     * @return object Services_Yahoo_Response Furigana result
     * @throws Services_Yahoo_Exception
     */
    public function submit()
    {
        $url = $this->requestURL . '?';

        foreach ($this->parameters as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $value2) {
                    $url .= $key . '=' . urlencode($value2) . '&';
                }
                continue;
            }

            $url .= $key . '=' . urlencode($value) . '&';
        }

        $request = new HTTP_Request($url);

        $result = $request->sendRequest();
        if (PEAR::isError($result)) {
            throw new Services_Yahoo_Exception($result->getMessage());
        }

        return new Services_Yahoo_JP_Furigana_Response($request);
    }

    /**
     * Set Application ID
     *
     * An Application ID is a string that uniquely identifies your
     * application. Think of it as like a User-Agent string. If you
     * have multiple applications, you should use a different ID for
     * each one. You can register your ID and make sure nobody is
     * already using your ID on Yahoo's Application ID registration
     * page.
     *
     * The ID defaults to "PEAR_Services_Y_JP", but you are free to
     * change it to whatever you want.  Please note that the access
     * to the Yahoo API is not limited via the Application ID but via
     * the IP address of the host where the package is used.
     *
     * @param string $id Application ID
     *
     * @return void
     */
    public function withAppID($id)
    {
        $this->parameters['appid'] = $id;
    }

    /**
     * Returns an element from the parameters
     *
     * @param string $name Name of the element
     *
     * @return string Value of the parameter idenfied by $name
     */
    protected function getParameter($name)
    {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }

        return '';
    }

    /**
     * Set the sentence
     *
     * @param string $sentence sentence to parse
     *
     * @return void
     */
    public function setSentence($sentence)
    {
        $this->parameters['sentence'] = $sentence;
    }

    /**
     * Set the grade
     *
     * @param string $grade the grade
     *
     * @return void
     */
    public function setGrade($grade)
    {
        if (preg_match('/^[1-8]$/', $grade) === 0) {
            throw new Services_Yahoo_Exception('Unknown grade ' . $grade);
        }
        $this->parameters['grade'] = $grade;
    }

}
?>
