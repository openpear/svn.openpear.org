<?php
require_once('Services/Twitter/Time.php');
$twitter = new Services_Twitter_Time();
echo $t->get(time() - 35990000);
echo "\n";
