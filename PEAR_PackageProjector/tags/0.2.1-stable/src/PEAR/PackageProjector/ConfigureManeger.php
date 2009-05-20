<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Creates PEAR Package in the way like the Command "make".
 * 
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 * 
 * @category   pear
 * @package    PEAR_PackageProjector
 * @author     Kouichi Sakamoto <sakamoto@servlet.sakura.ne.jp> 
 * @copyright  2007 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PEAR_PackageProjector
 * @since      File available since Release 0.1.0
 */

/**
 * 
 *
 * @category   pear
 * @package    PEAR_PackageProjector
 * @author     Kouichi Sakamoto <sakamoto@servlet.sakura.ne.jp> 
 * @copyright  2007 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 0.1.0
 * @link       http://pear.php.net/package/PEAR_PackageProjector
 * @since      Class available since Release 0.1.0
 */
class PEAR_PackageProjector_ConfigureManeger {
    private $groups;
    
    /**
     *
     */
    public function __construct()
    {
        $this->groups = array();
    }
    
    /**
     *
     */
    public function setting(PEAR_PackageProjector_ProjectInfo $projinfo, $confpath)
    {
        $conf = parse_ini_file($confpath, true);
        //
        foreach ($conf as $groupname=>$items) {
            $matches = array();
            $type   = $groupname;
            $target = null;
            if (preg_match("/^([a-z0-9\_\-]+:\/\/)(.+)/i", $groupname, $matches)) {
                $type   = $matches[1];
                $target = $matches[2];
            }
            if (isset($this->groups[$type])) {
                //
                $group = $this->groups[$type];
                $group->start($target, $confpath);
                foreach ($items as $key=>$value) {
                    $group->setting($projinfo, $key, $value);
                }
                $group->finish($projinfo);
            }
        }
    }

    /**
     *
     */
    public function addConfigure(PEAR_PackageProjector_Configure $confg )
    {
        $this->groups[$confg->getName()] = $confg;
    }

    /**
     *
     */
    static public function getBuildScriptTextWindows()
    {
        $text = "pearproj -t --configure ./build.conf --make -p ./";
        return $text;
    }

    /**
     *
     */
    static public function getBuildScriptTextUnix()
    {
        $text = "#!/bin/sh\n";
        $text .= "pearproj -t --configure ./build.conf --make -p ./";
        return $text;
    }

    /**
     *
     */
    static public function getBuildConfigureText($package_name)
    {
        $text = '[project]'."\n"
              . 'src_dir = src'."\n"
              . 'release_dir = release'."\n"
              . ''."\n"
              . '[package]'."\n"
              . 'package_name = '.$package_name."\n"
              . 'package_type = php'."\n"
              . 'baseinstalldir = /'."\n"
              . 'channel = __uri'."\n"
              . 'summary = #'."\n"
              . ';description = #'."\n"
              . ';notes = #'."\n"
              . ';summary_file = <filepath>'."\n"
              . 'description_file = desc.txt'."\n"
              . 'notes_file = notes.txt'."\n"
              . ''."\n"
              . '[role]'."\n"
              . ';; role value is <php|data|doc|test|script|src>'."\n"
              . ';sh = script'."\n"
              . ''."\n"
              . '[version]'."\n"
              . 'release_ver = 0.1.0'."\n"
              . 'release_stab = alpha'."\n"
              . 'api_ver = 0.1.0'."\n"
              . 'api_stab = alpha'."\n"
              . 'php_min = 5.1.0'."\n"
              . 'pear_min = 1.4.11'."\n"
              . ''."\n"
              . '[license]'."\n"
              . 'name =PHP License 3.01'."\n"
              . 'uri = http://www.php.net/license/3_01.txt'."\n"
              . ''."\n"
              . '[maintainer://handlename]'."\n"
              . 'name = fullname'."\n"
              . 'email = email@local.local'."\n"
              . 'role = lead'."\n"
              . ''."\n"
              . ';[file://<filepath>]'."\n"
              . ';commandscript = command'."\n"
              . ';ignore = 1'."\n"
              . ';platform = windows'."\n"
              . ';install = renamefile'."\n"
              . ';; role value is <php|data|doc|test|script|src>'."\n"
              . ';role = script'."\n"
              . ''."\n"
              . ';[dep://<packagename>]'."\n"
              . ';; type: <required|optional>'."\n"
              . ';type = optional'."\n"
              . ';; channel: pear.php.net or __uri or etc...'."\n"
              . ';channel = pear.php.net'."\n"
              . ';min = 0'."\n"
              . ';max = 0'."\n"
              . ';recommended = 0'."\n"
              . ';exclude = 0'."\n"
              . ';providesextension = 0'."\n"
              . ';nodefault = 0'."\n"
              . ''."\n"
              . ';[installer://<groupname>]'."\n"
              . ';instructions = <group info message>'."\n"
              . ';<param_name>.prompt = <param info message>'."\n"
              . ';<param_name>.type = string'."\n"
              . ';<param_name>.default = <default value>'."\n"
              . ''."\n"
              ;
        return $text;
    }

    /**
     *
     */
    static public function getPostInstallerText($name, $groups)
    {
        $text = '<?php'."\n"
              . 'class '.$name."\n"
              . '{'."\n"
              . ''."\n"
              . '    // {{{ $_config'."\n"
              . ''."\n"
              . '    /**'."\n"
              . '     * PEAR_Config object '."\n"
              . '     * '."\n"
              . '     * @var object(PEAR_Config)'."\n"
              . '     * @access protected'."\n"
              . '     */'."\n"
              . '    private $_config;'."\n"
              . ''."\n"
              . '    // }}}'."\n"
              . '    // {{{ $_ui'."\n"
              . ''."\n"
              . '    /**'."\n"
              . '     * PEAR_Installer_Ui '."\n"
              . '     * '."\n"
              . '     * @var object(PEAR_Installer_Ui)'."\n"
              . '     * @access protected'."\n"
              . '     */'."\n"
              . '    private $_ui;'."\n"
              . ''."\n"
              . '    // }}}'."\n"
              . ''."\n"
              . '    // {{{ init()'."\n"
              . ''."\n"
              . '    /**'."\n"
              . '     * init install.'."\n"
              . '     *'."\n"
              . '     * @link http://pear.php.net/package/PEAR/docs/1.4.4/PEAR/PEAR_Config.html'."\n"
              . '     * @link http://pear.php.net/package/PEAR/docs/1.4.4/PEAR/PEAR_PackageFile_v2.html'."\n"
              . '     * '."\n"
              . '     * @param object(PEAR_Config) $config'."\n"
              . '     * @param object(PEAR_PackageFile_v2) $self'."\n"
              . '     * @param string $lastInstalledVersion'."\n"
              . '     * @access public'."\n"
              . '     * @return bool True if initialized successfully, otherwise false.'."\n"
              . '     */'."\n"
              . '    function init(&$config, $self, $lastInstalledVersion = null)'."\n"
              . '    {'."\n"
              . '       $this->_config = &$config;'."\n"
              . '       $this->_ui = &PEAR_Frontend::singleton();'."\n"
              . '       '."\n"
              . '       return true;'."\n"
              . '    }'."\n"
              . ''."\n"
              . '    // }}}'."\n"
              . '    // {{{ run()'."\n"
              . ''."\n"
              . '    /**'."\n"
              . '     * Run install.'."\n"
              . '     * '."\n"
              . '     * @param array $infoArray'."\n"
              . '     * @param string $paramGroup'."\n"
              . '     * @access public'."\n"
              . '     * @return bool'."\n"
              . '     */'."\n"
              . '    function run($infoArray, $paramGroup)'."\n"
              . '    {'."\n"
              . '        if (\'_undoOnError\' == $paramGroup) {'."\n"
              . '            $this->_ui->outputData(\'An error occured during installation.\');'."\n"
              . '            return false;'."\n"
              . '        }'."\n"
              . '        '."\n"
              . '        $method = \'run_\'.$paramGroup;'."\n"
              . '        if (method_exists($this, $method)) {'."\n"
              . '            return $this->$method($infoArray);'."\n"
              . '        }'."\n"
              . '        '."\n"
              . '        $this->_ui->outputData(\'ERROR: Unknown parameter group <\'.$paramGroup.\'>.\');'."\n"
              . '        return false;'."\n"
              . '    }'."\n"
              . ''."\n"
              . '    // }}}'."\n";
              //
              foreach ($groups as $num=>$groupname) {
                  $text .= self::getPostInstallerMethodText($groupname);
              }

        return $text. '}'."\n". '?>'."\n";
    }

    /**
     *
     */
    static public function getPostInstallerMethodText($groupname)
    {
        $text = '    // {{{ run_'.$groupname.'()'."\n"
              . ''."\n"
              . '    /**'."\n"
              . '     * Run '.$groupname.' install.'."\n"
              . '     * '."\n"
              . '     * @param array $infoArray'."\n"
              . '     * @access public'."\n"
              . '     * @return bool'."\n"
              . '     */'."\n"
              . '    function run_'.$groupname.'($infoArray)'."\n"
              . '    {'."\n"
              . '        return true;'."\n"
              . '    }'."\n"
              . ''."\n"
              . '    // }}}'."\n"
              ;
              return $text;
    }
}

/*
 * Local Variables:
 * mode: php
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
?>
