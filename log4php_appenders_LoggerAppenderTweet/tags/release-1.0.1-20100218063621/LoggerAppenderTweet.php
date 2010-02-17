<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *
 * @package log4php
 * @subpackage appenders
 */

/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__) . '/..');
 
if (!defined('LOG4PHP_TWITTER_ID')) exit('Undefined LOG4PHP_TWITTER_ID');
if (!defined('LOG4PHP_TWITTER_PASSWORD')) exit('Undefined LOG4PHP_TWITTER_PASSWORD');
/**
 */
require_once(LOG4PHP_DIR . '/LoggerAppenderSkeleton.php');
require_once(LOG4PHP_DIR . '/LoggerLog.php');
require_once('Zend/Service/Twitter.php');

/**
 * LoggerAppenderTweet
 *
 * @author  Yohei Sato <yokkuns@tkul.jp>
 * @version $Revision: $
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderTweet extends LoggerAppenderSkeleton
{

    /**
     * @var bool requiresLayout
     */
    protected $requiresLayout = true;

    /**
     * @var Zend_Service_Twitter twitter connection
     */
    protected $twitter;

    /**
     * constructor
     *
     * @param string string $name appender name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->twitter = new Zend_Service_Twitter(LOG4PHP_TWITTER_ID, LOG4PHP_TWITTER_PASSWORD);
    }

    /**
     * activateOptions
     *
     * @return void
     */
    public function activateOptions()
    {
        $this->closed = false;
        return;
    }

    /**
     * close
     *
     * @return void
     */
    public function close()
    {
        $this->closed = true;
    }

    /**
     * append
     *
     * @param LoggerLoggingEvent $event
     * @return void
     */
    function append($event)
    {
        LoggerLog::debug("LoggerAppenderTweet::append()");
        if ($this->layout !== null) {
            if( $msg = $this->layout->format($event) ){
                $this->twitter->status->update($msg);
            }
        }
    }
}

