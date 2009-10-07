<?php
class Wozozo_Saigoppe
{
    private $onomatopoeia = 'プゥゥゥゥゥ';
    private $ascii_art = 'or2=3';
    private $enable_aa;
    
    public function __construct($onomatopoeia=null, $enable_aa = true)
    {
        $onomatopoeia and $this->onomatopoeia = $onomatopoeia;
        $this->enable_aa = $enable_aa;
        register_shutdown_function(array($this, 'poot'));
    }

    public function poot()
    {
        echo empty($this->ascii_art) || !$this->enable_aa ? '' : $this->ascii_art . ' ' ,$this->onomatopoeia, PHP_EOL;
    }

    public function setOnomatopoeia($onomatopoeia)
    {
        $this->onomatopoeia = $onomatopoeia;
    }

    public function setAsciiArt($ascii_art)
    {
        $this->ascii_art = $ascii_art;
    }

    public function enableAsciiArt($enable=true)
    {
        $this->enable_aa = $enable;
    }
}
?>
