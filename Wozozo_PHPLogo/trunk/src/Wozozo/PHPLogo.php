<?php
/**
 * PHPLogo.php
 *
 */

class Wozozo_PHPLogo
{
    private $logo = array(
        'PHP_LOGO_GUID'     => 'PHPE9568F34-D428-11d2-A769-00AA001ACF42',
        'PHP_EGG_LOGO_GUID' => 'PHPE9568F36-D428-11d2-A769-00AA001ACF42',
        'ZEND_LOGO_GUID'    => 'PHPE9568F35-D428-11d2-A769-00AA001ACF42',
    );

    /**
     * getLogoGUID
     *
     */
    public function getLogoGUID($type = NULL)
    {
        if (in_array($type, array_keys($this->logo))) {
            return $this->logo[$type];
        } else {
            return array_rand(array_flip($this->logo));
        }
    }
}

// example

/*
if (ini_get('expose_php') !== "1") {
    throw new Exception('expose_php should be on.');
}

$wozozo = new Wozozo_PHPLogo();
//$guid = $wozozo->getLogoGUID();
$guid = $wozozo->getLogoGUID('PHP_LOGO_GUID');
echo "<img src=\"{$_SERVER['PHP_SELF']}?={$guid}\" />";
 */
