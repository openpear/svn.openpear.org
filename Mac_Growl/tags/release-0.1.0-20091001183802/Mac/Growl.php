<?php
/**
 * The class for Growl notification via AppleScript and GrowlHelperApp.
 *
 * PHP version 5
 *
 * Copyright (c) 2009 Ryusuke SEKIYAMA, All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any personobtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @category    Mac
 * @package     Mac_Growl
 * @author      Ryusuke SEKIYAMA <rsky0711@gmail.com>
 * @copyright   2009 Ryusuke SEKIYAMA
 * @license     http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version     SVN: $Id$
 * @link        http://openpear.org/package/Mac_Growl
 */

require_once 'Mac/AppleScript.php';

// {{{ Mac_Growl

/**
 * The class for Growl notification via AppleScript and GrowlHelperApp.
 *
 * @category    Mac
 * @package     Mac_Growl
 * @author      Ryusuke SEKIYAMA <rsky0711@gmail.com>
 * @version     Release: @package_version@
 * @link        http://openpear.org/package/Mac_Growl
 */
class Mac_Growl
{
    // {{{ constants

    const GROWL_HELPER_APP = 'GrowlHelperApp';

    // }}}
    // {{{ properties

    private $_applicationName;
    private $_notifications;
    private $_lastScript;

    // }}}
    // {{{ __construct()

    /**
     * Constructor.
     *
     * @param string $applicationName
     * @param array $notifications
     */
    public function __construct($applicationName, array $notifications)
    {
        $this->_applicationName = $applicationName;
        $this->_notifications = $notifications;
    }

    // }}}
    // {{{ register()

    /**
     * Registers the application.
     *
     * @param array|string $defaultNotifications
     * @return void
     */
    public function register($defaultNotifications = null)
    {
        if ($defaultNotifications !== null) {
            if (is_string($defaultNotifications)) {
                $defaultNotifications = array($defaultNotifications);
            }
            $defaultNotifications = array_intersect($this->_notifications,
                                                    $defaultNotifications);
        } else {
            $defaultNotifications = $this->_notifications;
        }

        $script = new Mac_AppleScript();
        $script->tellApplication(self::GROWL_HELPER_APP, Mac_AppleScript::APPEND_BREAK)
            ->registerAsApplication($this->_applicationName)
            ->allNotifications($this->_notifications)
            ->defaultNotifications($defaultNotifications)
            ->endTell(null, Mac_AppleScript::PREPEND_BREAK, true);
        $this->_lastScript = (string)$script;
    }

    // }}}
    // {{{ notify()

    /**
     * Notifies with Growl.
     *
     * @param string $notification
     * @param string $title
     * @param string $description
     * @param array $options
     * @return void
     */
    public function notify($notification, $title, $description, array $options = null)
    {
        if (!in_array($notification, $this->_notifications)) {
            return; // @todo throw an exception
        }

        $script = new Mac_AppleScript();
        $script->tellApplication(self::GROWL_HELPER_APP, Mac_AppleScript::APPEND_BREAK)
            ->notifyWithName($notification)
            ->title($title)
            ->description($description)
            ->applicationName($this->_applicationName);

        if ($options) {
            if (array_key_exists('sticky', $options)) {
                $script->sticky((bool)$options['sticky']);
            }

            if (array_key_exists('priority', $options)) {
                if (is_numeric($options['priority'])) {
                    $script->priority((int)$options['priority']);
                } else {
                    // @todo throw an exception
                }
            }

            if (array_key_exists('icon', $options)) {
                $icon = $options['icon'];
                if (is_file($icon) && is_readable($icon)) {
                    $script->imageFromLocation('file://' . realpath($icon));
                } else {
                    // @todo throw an exception
                }
            }
        }
        $script->endTell(null, Mac_AppleScript::PREPEND_BREAK, true);
        $this->_lastScript = (string)$script;
    }

    // }}}
    // {{{ getApplicationName()

    /**
     * Gets the application name.
     *
     * @param void
     * @return string
     */
    public function getApplicationName()
    {
        return $this->_applicationName;
    }

    // }}}
    // {{{ getNotifications()

    /**
     * Gets the list of the notifications.
     *
     * @param void
     * @return array
     */
    public function getNotifications()
    {
        return $this->_notifications;
    }

    // }}}
    // {{{ getLastScript()

    /**
     * Gets the last executed script.
     *
     * @param void
     * @return string
     */
    public function getLastScript()
    {
        return $this->_lastScript;
    }

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
