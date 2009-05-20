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
class PEAR_PackageProjector_ProjectInfo implements PEAR_PackageProjector_Visitor {
    private $items;
    private $Maintainers;
    private $Dependencies;
    private $Roles;
    private $AttributeManager;
    private $InstallGroups;
    private $projectinfo;

    /**
     *
     */
    public function __construct()
    {
        $this->items  = array();
        $this->projectinfo  = array();
        $this->Maintainers  = new PEAR_PackageProjector_ProjectInfo_Maintainers();
        $this->Dependencies = new PEAR_PackageProjector_ProjectInfo_Dependencies();
        $this->Roles        = new PEAR_PackageProjector_ProjectInfo_Roles();
        $this->InstallGroups    = new PEAR_PackageProjector_ProjectInfo_InstallGroups();
        $this->AttributeManager = new PEAR_PackageProjector_ProjectInfo_AttributeManager();
        //
        $this->setProjectSrcDir('src');
        $this->setProjectReleaseDir('release');
        
    }
    
    /**
     *
     */
    public function setPackageName($name)
    {
        $this->items['PackageName'] = new PEAR_PackageProjector_ProjectInfo_PackageName($name);
    }
    
    /**
     *
     */
    public function setPackageType($type)
    {
        $this->items['PackageType'] = new PEAR_PackageProjector_ProjectInfo_PackageType($type);
    }
    
    /**
     *
     */
    public function setBaseInstallDir($path)
    {
        $this->items['BaseInstallDir'] = new PEAR_PackageProjector_ProjectInfo_BaseInstallDir($path);
    }
    
    /**
     *
     */
    public function setSummary($summary)
    {
        $this->items['Summary'] = new PEAR_PackageProjector_ProjectInfo_Summary($summary);
    }
    
    /**
     *
     */
    public function setNotes($notes)
    {
        $this->items['Notes'] = new PEAR_PackageProjector_ProjectInfo_Notes($notes);
    }
    
    /**
     *
     */
    public function setDescription($description)
    {
        $this->items['Description'] = new PEAR_PackageProjector_ProjectInfo_Description($description);
    }
    
    /**
     *
     */
    public function setChannel($channel)
    {
        $this->items['Channel'] = new PEAR_PackageProjector_ProjectInfo_Channel($channel);
    }

    /**
     *
     */
    public function setLicense($license, $url)
    {
        $this->items['License'] = new PEAR_PackageProjector_ProjectInfo_License($license, $url);
    }

    /**
     *
     */
    public function setReleaseVersion($version, $stability)
    {
        $this->items['ReleaseVersion'] = new PEAR_PackageProjector_ProjectInfo_ReleaseVersion($version, $stability);
    }

    /**
     *
     */
    public function setAPIVersion($version, $stability)
    {
        $this->items['APIVersion'] = new PEAR_PackageProjector_ProjectInfo_APIVersion($version, $stability);
    }

    /**
     *
     */
    public function setPhpDep(PEAR_PackageProjector_ProjectInfo_PhpDep $dep)
    {
        $this->items['PhpDep'] = $dep;
    }

    /**
     *
     */
    public function setPearinstallerDep(PEAR_PackageProjector_ProjectInfo_PearinstallerDep $dep)
    {
        $this->items['PearinstallerDep'] = $dep;
    }

    /**
     *
     */
    public function addMaintainer(PEAR_PackageProjector_ProjectInfo_Maintainer $maintainer)
    {
        $this->Maintainers->add($maintainer);
    }

    /**
     *
     */
    public function addPackageDepWithChannel(PEAR_PackageProjector_ProjectInfo_Dependency $dependency)
    {
        $this->Dependencies->add($dependency);
    }

    /**
     *
     */
    public function addRole($extension, $role)
    {
        $this->Roles->add(new PEAR_PackageProjector_ProjectInfo_Role($extension, $role));
    }

    /**
     *
     */
    public function addInstallGroup(PEAR_PackageProjector_ProjectInfo_InstallGroup $group)
    {
        $this->InstallGroups->add($group);
    }

    /**
     *
     */
    public function setProjectReleaseDir($dir)
    {
        $this->projectinfo['relasedir'] = $dir;
    }

    /**
     *
     */
    public function setProjectSrcDir($dir)
    {
        $this->projectinfo['srcdir'] = $dir;
    }

    /**
     *
     */
    public function getProjectReleaseDir()
    {
        return $this->projectinfo['relasedir'];
    }

    /**
     *
     */
    public function getProjectSrcDir()
    {
        return $this->projectinfo['srcdir'];
    }

    /**
     *
     */
    public function getAttribute($path)
    {
        return $this->AttributeManager->getAttribute($path);
    }
    
    /**
     *
     */
    public function visit(PEAR_PackageProjector_Package $package)
    {
        //
        foreach ($this->items as $key=>$item) {
            $package->accept($item);
        }
        //
        $package->accept($this->Roles);
        $package->accept($this->Maintainers);
        $package->accept($this->AttributeManager);
        $package->accept($this->Dependencies);
        $package->accept($this->InstallGroups);
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
