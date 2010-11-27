PHP library for access Symfony2Bundles
http://symfony2bundles.org/api

Support only Zend Framework 1 (Not zf2!)

USE with zf command
========

    zf enable config.provider Keion\\Service\\Symfony2Bundles\\Tool\\Symfony2BundlesProvider

    zf get-bundles keion\service\symfony2-bundles\tool\symfony2bundles sort[=name] format
    zf get-bundle keion\service\symfony2-bundles\tool\symfony2bundles username name format
    zf get-projects keion\service\symfony2-bundles\tool\symfony2bundles sort format
    zf get-project keion\service\symfony2-bundles\tool\symfony2bundles username name format
    zf search keion\service\symfony2-bundles\tool\symfony2bundles query format
    zf get-developers keion\service\symfony2-bundles\tool\symfony2bundles sort format
    zf get-developer keion\service\symfony2-bundles\tool\symfony2bundles name format
    zf get-developer-bundles keion\service\symfony2-bundles\tool\symfony2bundles name format
    zf get-developer-projects keion\service\symfony2-bundles\tool\symfony2bundles name format

