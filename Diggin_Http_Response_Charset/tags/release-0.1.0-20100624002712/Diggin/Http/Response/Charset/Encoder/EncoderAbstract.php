<?php

/**
 * Diggin_Http_Response_Charset
 * 
 * a part of this package (Diggin_Http_Response_Charset_Detector_Html) is
 * borrowed from HTMLScraping
 * 
 * @see http://www.rcdtokyo.com/etc/htmlscraping/
 *
 * LICENSE: This source file is subject to the GNU Lesser General Public
 * License as published by the Free Software Foundation;
 * either version 2.1 of the License, or any later version
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl.html
 * If you did not have a copy of the GNU Lesser General Public License
 * and are unable to obtain it through the web, please write to
 * the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 * ---------------------------------------------------------------------
 */

require_once 'Diggin/Http/Response/Charset/Encoder/EncoderInterface.php';

abstract class Diggin_Http_Response_Charset_Encoder_EncoderAbstract
    implements Diggin_Http_Response_Charset_Encoder_EncoderInterface
{
    /**
     * encode response
     * 
     * @param mixed
     * Zend_Http_Response | array('content-type' => $var, 'body' => $html) | string $html
     * (@param string $convert_vars)
     * 
     * @return mixed
     */
    final public function encode($resouce, $remains = null)
    {
        //if ($resouce instanceof Zend_Http_Response)
        $ctype = null;
        if (is_array($resouce)) {
            $ctype = $resouce['content-type'];
            $body  = $resouce['body'];
        } elseif (is_string($resouce)) {
            $body = $resouce;
        }
        
        $encoding_from = $this->_encodingFrom($body, $ctype);
        $body = $this->_initBody($body);

        if ($remains) {
            @mb_convert_variables('UTF-8', $encoding_from, $body, $remains);

            return array($body, $remains);
        } else {
            $body = mb_convert_encoding($body, 'UTF-8', $encoding_from);
            return $body;
        }
    }

    abstract protected function _encodingFrom($body, $ctype);
    
    protected function _initBody($body)
    {
        return $body;
    }
}
