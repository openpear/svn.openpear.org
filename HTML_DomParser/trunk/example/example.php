<?php
require "HTML/DomParser.php";

$dom = HTML_DomParser::loadString("<html><body><div id='test'>moe</div></body></html>");
$node = $dom->find("#test",0);
echo $node->innerText();
