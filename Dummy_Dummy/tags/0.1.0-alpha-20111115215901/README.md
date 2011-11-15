fwComponents\Dummy
==================

The dummy class "Dummy" and the dummy exception "DummyException" do not have a special purpose.
This package only demonstrates the "best-practice" how-to structure a PHP project.

Requirements
------------

* fwComponents\Dummy requires PHP 5.3.0 (or later).
* [PHPUnit](http://github.com/sebastianbergmann/phpunit), the de-facto standard for unit testing in PHP projects is required to run the tests of the project.

Installation
------------

fwComponents\Dummy should be installed using the PEAR Installer, the backbone of the PHP Extension and Application Repository that provides a distribution system for PHP packages.

Depending on your OS distribution and/or your PHP environment, you may need to install PEAR or update your existing PEAR installation before you can proceed with the following instructions. `sudo pear upgrade PEAR` usually suffices to upgrade an existing PEAR installation. The [PEAR Manual ](http://pear.php.net/manual/en/installation.getting.php) explains how to perform a fresh installation of PEAR.

The following two commands (which you may have to run as `root`) are all that is required to install PHPUnit using the PEAR Installer:

    pear channel-discover pear.tehhahn.github.com
    pear install fw/Dummy

After the installation you can find the fwComponent\Dummy source files inside your local PEAR directory; the path is usually `/usr/lib/php/fw/Component/Dummy`.
