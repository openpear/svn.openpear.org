@echo off
REM @package PEAR_PackageProjector
REM @author Kouichi Sakamoto

"@php_bin@" -d include_path="@php_dir@" "@bin_dir@\pearproj" %*
