<?php
/**
 * Simple dispatcher class
 *
 * PHP version 5.2
 *
 * Copyright (c) 2009 Shinya Ohyanagi, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Shinya Ohyanagi nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Net
 * @package   Net_URL_Dispatcher
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * @see prepare
 */
require_once 'prepare.php';

/**
 * DescribeDispatcher
 *
 * @category  Net
 * @package   Net_URL_Dispatcher
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class DescribeDispatcher extends SpecCommon
{
    public function before()
    {
    }

    public function after()
    {
    }

    public function itShouldGetVersion()
    {
        $this->spec(Net_URL_Dispatcher::VERSION)->should->be('0.1.1');
    }

    public function itShouldCreateInstance()
    {
        $dispatcher = new Net_URL_Dispatcher();
        $this->spec($dispatcher)->should->beAnInstanceOf('Net_URL_Dispatcher');
    }

    public function itShouldSetDirecotryPath()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $dispatcher->setDirectory($path);
        $exceptPath = $dispatcher->getDirectory();

        $this->spec($exceptPath)->should->be($path);
    }

    public function itShouldSetDirecotryPathInDispatchMethod()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/index';

        ob_start();
        $dispatcher->connect(':controller/:action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($dispatcher->getDirectory())->should->be($path);
    }

    public function itShouldDefaultErrorReportingBeE_all()
    {
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $this->spec(error_reporting())->should->be(E_ALL);
    }

    public function itShouldSetErrorReporting()
    {
        $dispatcher = new Net_URL_Dispatcher();
        $dispatcher->setErrorReporting(E_ALL|E_NOTICE);

        $this->spec(error_reporting())->should->be(E_ALL|E_NOTICE);
    }

    public function itShouldSetParams()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/mix/hoge/fuga';

        ob_start();
        $dispatcher->setParams(array('foo' => 'bar'))->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('fuga_bar');
    }

    public function itShouldCallNet_url_mapperClassMethodWhichHasRetrurnValue()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $buffer     = $dispatcher->getId();

        $this->spec($buffer)->should->be(__METHOD__);
    }

    public function itShouldCallNet_url_mapperClassMethodWhichHasNoReturnValue()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $buffer     = $dispatcher->setScriptname('index.php');

        $this->spec($buffer)->should->beAnInstanceOf('Net_URL_Dispatcher');

    }
}
