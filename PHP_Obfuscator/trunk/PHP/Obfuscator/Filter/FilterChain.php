<?php
require_once 'PHP/Obfuscator/Encoder/EncoderChain.php';
require_once 'PHP/Obfuscator/Filter/ExecutionFilter.php';
class PHP_Obfuscator_Filter_FilterChain
{
    private $filters = array();
    private $encoder_chain;
    private $next_length;
    public function __construct($code, PHP_Obfuscator_Encoder_EncoderChain $encoder_chain) {
        $this->encoder_chain = $encoder_chain;

        $obj = new PHP_Obfuscator_Filter_ExecutionFilter();
        $obj->setArgs(array($code));
        $this->filters[] = $obj;
    }
    public function add(PHP_Obfuscator_Filter_Filter $filter) {
        if (!($filter instanceof PHP_Obfuscator_Filter_ExecutionFilter)) {
            $this->filters[] = $filter;
        }
        return $this;
    }
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
