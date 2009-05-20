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
class PEAR_PackageProjector_Project {
    private $ProjectInfo;
    private $ProjectDirectory;

    /**
     *
     */
    public function __construct($projectfile, $f_usetmp, $mod=0000)
    {
        if (0<$mod) {
            $this->_createProject($projectfile, $mod, $f_usetmp);
        } else {
            $this->_loadProject($projectfile, $f_usetmp);
        }
    }
    
    /**
     *
     */
    public function save()
    {
        $projectfile = $this->ProjectDirectory->getProjectFilePath();
        file_put_contents($projectfile, serialize($this->ProjectInfo));
    }
    
    /**
     *
     */
    public function info()
    {
        return $this->ProjectInfo;
    }

    /**
     *
     */
    public function make()
    {
        $this->save();
        $this->_buildProject();
    }
    
    /**
     *
     */
    public function clear()
    {
        $this->ProjectInfo = new PEAR_PackageProjector_ProjectInfo();
        $this->save();
    }
    
    /**
     *
     */
    public function configure($conf)
    {
        $this->clear();
        $confpath = PEAR_PackageProjector_Derictory::getRealpath($conf);
        //
        $handler = PEAR_PackageProjector::singleton()->getMessageHandler();
        $handler->buildMessage(5, "*** Configuring package. ***", true);
        PEAR_PackageProjector::singleton()->configure($this->ProjectInfo, $confpath);
        //
        if (!$this->ProjectDirectory->loadSetting($this->ProjectInfo, 0)) {
            return ;
        }
        $this->save();
        $handler->buildMessage(5, "", true);
    }
    
    /**
     *
     */
    public function checkcode()
    {
        //
        if (!$this->ProjectDirectory->loadSetting($this->ProjectInfo, 0)) {
            return ;
        }
        $oldcwd = getcwd();
        $verbose = false;
        $files = $this->ProjectDirectory->getSrcPath();
        $standard = 'PEAR';
        $report = '';
        $showWarnings = false;

        $handler = PEAR_PackageProjector::singleton()->getMessageHandler();
        /*
         * execute code sniffer
         */
        $handler->buildMessage(5, "*** Checks source in CodeSniffer. ***", true);
        //
        ob_start();
        $phpcs = new PHP_CodeSniffer($verbose);
        $phpcs->process($files, $standard);
        if ($report === 'summary') {
            $phpcs->printErrorReportSummary($showWarnings);
        } else {
            $phpcs->printErrorReport($showWarnings);
        }
        $buff = ob_get_contents();
        ob_end_clean();
        $handler->buildMessage(5, $buff, true);
        
        chdir($oldcwd);
        
        return (0==strlen($buff));
    }

    /**
     *
     */
    private function _createProject($projectpath, $mod, $f_usetmp)
    {
        $handler = PEAR_PackageProjector::singleton()->getMessageHandler();
        //
        $this->ProjectInfo = new PEAR_PackageProjector_ProjectInfo();
        
        //
        $this->ProjectDirectory = new PEAR_PackageProjector_Derictory($projectpath, $f_usetmp);
        if (false === $this->ProjectDirectory->checkCreateProject()) {
            return;
        }
        
        //
        if (!$this->ProjectDirectory->loadSetting($this->ProjectInfo, $mod)) {
            return ;
        }
        //
        $pathinfo = pathinfo($projectpath);
        //
        $this->ProjectDirectory->createBuildConf($pathinfo['basename']);
        $this->ProjectDirectory->createBuildScript();
        //$this->ProjectDirectory->createReadme();
        $this->ProjectDirectory->createSrcDir($pathinfo['basename']);
        $this->ProjectDirectory->createBaseSrc($pathinfo['basename']);
        $this->ProjectDirectory->createSampleSrc($pathinfo['basename']);
        $this->ProjectDirectory->createNotesText();
        $this->ProjectDirectory->createDescText();
        
        //
        //$this->save();
        //
        $handler->buildMessage(5, "create project directory ".$this->ProjectDirectory->getBaseDir().".", true);
    }
        
    /**
     *
     */
    private function _loadProject($projectpath, $f_usetmp)
    {
        //
        $this->ProjectDirectory = new PEAR_PackageProjector_Derictory($projectpath, $f_usetmp);
        if (false === $this->ProjectDirectory->checkLoadProject()) {
            return;
        }

        //
        $projectfile = $this->ProjectDirectory->getProjectFilePath();
        if (file_exists($projectfile)) {
            $this->ProjectInfo = unserialize(file_get_contents($projectfile));
        } else {
            $this->ProjectInfo = new PEAR_PackageProjector_ProjectInfo();
            $this->save();
        }
                
        return true;
    }
    
    /**
     *
     */
    private function _buildProject()
    {
        //
        if (!$this->ProjectDirectory->loadSetting($this->ProjectInfo, 0)) {
            return ;
        }
        $package = new PEAR_PackageProjector_Package();
        $oldcwd  = getcwd();
        $handler = PEAR_PackageProjector::singleton()->getMessageHandler();
        //
        try {
            chdir($this->ProjectDirectory->getSrcPath());
            /*
             * Create package2.xml
             */
            $handler->buildMessage(5, "*** Create package2.xml ***", true);
            //
            $package->accept($this->ProjectDirectory->getPackageDirectory());
            $package->accept($this->ProjectInfo);
            $package->build();
            $handler->buildMessage(5, "", true);

            /*
             * Create Tgz
             */
            $handler->buildMessage(5, "*** Create Tgz File ***", true);
            //
            chdir($this->ProjectDirectory->getRelasePath());
            $pkg = new PEAR_PackageProjector_Packager();
            $pkg->setMessageHandler($handler);
            $pkg->package($this->ProjectDirectory->getPackageFile());
            $handler->buildMessage(5, "", true);
            
        } catch(Exception $e) {
            chdir($oldcwd);
            $handler->buildMessage(5, "\n*** Stop for the exception was generated.  ***", true);
            throw $e;
        }
        
        $handler->buildMessage(5, "*** Finish ***", true);

        //
        chdir($oldcwd);
        
        //
        return true;
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
