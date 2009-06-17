<?php
/**
 *  Ether_Plugin_Filter_EthnaVersion.php
 *
 *  @author     Sotaro KARASAWA <sotaro.k@gmail.com>
 *  @package    Ether
 */

/**
 *  EthnaVersion Plugin Filter
 *
 *  @description    Ethre plugin for display Ethna version
 *  @author         Sotaro KARASAWA <sotaro.k@gmail.com>
 *  @access         public
 *  @package        Ether
 */
class Ether_Plugin_Filter_EthnaVersion extends Ethna_Plugin_Filter
{

    /**
     *  filter which will be executed at the end.
     *
     *  @access public
     */
    function postFilter()
    {
        echo '<div class="ethna-debug" id="ethna-debug-evwindow">';
        echo '<div class="ethna-debug-title">' . ETHNA_VERSION . '</div>';
        echo "<div class=\"ethna-debug-log\">";
        echo ETHNA_VERSION;
        echo <<<EOF
EOF;
        echo "</div> \n";
        echo '</div>';
    }

}
?>
