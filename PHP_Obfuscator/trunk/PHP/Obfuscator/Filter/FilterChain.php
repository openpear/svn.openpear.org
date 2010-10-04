<?php
/**
 * PHP_Obfuscator_Filter_FilterChain class
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy
 * the PHP License and are unable to obtain it through the web,
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  PHP
 * @package   PHP_Obfuscator
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2010 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   SVN: $Id$
 * @link      http://openpear.org/package/PHP_Obfuscator
 */

require_once 'PHP/Obfuscator/Encoder/EncoderChain.php';
require_once 'PHP/Obfuscator/Filter/ExecutionFilter.php';

/**
 * PHP_Obfuscator_Filter_FilterChain class
 *
 * @category  PHP
 * @package   PHP_Obfuscator
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2010 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/PHP_Obfuscator
 */
class PHP_Obfuscator_Filter_FilterChain
{

    /**
     * the filter chain
     * @var    array
     * @access private
     */
    private $filters = array();

    /**
     * the encoder chain
     * @var    mixed
     * @access private
     */
    private $encoder_chain;

    /**
     * the starting position of next encoded code fragment
     * @var    int
     * @access private
     */
    private $next_length;

    /**
     * constructor
     *
     * @param  string $code                                              an initial code fragment
     * @param  object PHP_Obfuscator_Encoder_EncoderChain $encoder_chain PHP_Obfuscator_Encoder_EncoderChain object
     * @return void
     * @access public
     */
    public function __construct($code, PHP_Obfuscator_Encoder_EncoderChain $encoder_chain) {
        $this->encoder_chain = $encoder_chain;

        $obj = new PHP_Obfuscator_Filter_ExecutionFilter();
        $obj->setArgs(array($code));
        $this->filters[] = $obj;
    }

    /**
     * Add filter
     *
     * @param  object PHP_Obfuscator_Filter_Filter $filter a filter object
     * @return object PHP_Obfuscator_Filter_FilterChain PHP_Obfuscator_Filter_FilterChain object
     * @access public
     */
    public function add(PHP_Obfuscator_Filter_Filter $filter) {
        if (!($filter instanceof PHP_Obfuscator_Filter_ExecutionFilter)) {
            $this->filters[] = $filter;
        }
        return $this;
    }

    /**
     * Build encoded code
     *
     * @return string Return encoded code
     * @access public
     */
    public function process() {
        $blocks = $this->buildBlocks();
        krsort($blocks);
        return '<?php $__n = __FILE__; eval(base64_decode("' . str_replace('=', '', base64_encode($this->buildBootstrap($blocks))) . '")); return; ?>' . PHP_EOL . join($blocks, '');
    }

    private function buildBootstrap() {
        return sprintf(
                   '$__f = fopen($__n, "rb"); fgets($__f); eval(%s);',
                   $this->encoder_chain->decode(sprintf('fread($__f, %d)', $this->next_length)));
    }

    private function buildBlocks() {
        $result = array();
        foreach ($this->filters as $filter) {
            $snippet = $this->encoder_chain->encode($filter->getCode() . ($filter instanceof PHP_Obfuscator_Filter_ExecutionFilter ? '' : sprintf('eval(' . $this->encoder_chain->decode('fread($__f, %d)') . ');', $this->next_length)));
            $result[] = $snippet;
            $this->next_length = strlen($snippet);
        }
        return $result;
    }
}
