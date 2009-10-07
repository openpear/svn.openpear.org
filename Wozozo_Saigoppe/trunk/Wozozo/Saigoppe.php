<?php
class Wozozo_Saigoppe
{
    private $onomatopoeia = 'プゥゥゥゥゥ';

    public function __construct($onomatopoeia=null)
    {
        $onomatopoeia and $this->onomatopoeia = $onomatopoeia;
        register_shutdown_function(array($this, 'poot'));
    }

    public function poot()
    {
        echo $this->onomatopoeia, PHP_EOL;
    }

    public function setOnomatopoeia($onomatopoeia)
    {
        $this->onomatopoeia = $onomatopoeia;
    }
}
?>
