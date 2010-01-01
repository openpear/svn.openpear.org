<?php
/**
 * Simple dispatcher class
 *
 * PHP version 5.2
 *
 * Copyright (c) 2009-2010 Shinya Ohyanagi, All rights reserved.
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
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * @see prepare
 */
require_once dirname(dirname(__FILE__)) . '/prepare.php';

/**
 * DescribeDispatcher
 *
 * @category  Net
 * @package   Net_URL_Dispatcher
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class DescribeActionDispatcher extends SpecCommon
{
    public function before()
    {
    }

    public function after()
    {
    }

    public function itShouldDispatchToActionClassByServerEnv()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge';

        ob_start();
        $dispatcher->connect(':action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('HogeAction_execute');
    }

    public function itShouldDispatchToActionClassBySetterMethod()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $dispatcher->setPathInfo('hoge');

        ob_start();
        $dispatcher->connect(':action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('HogeAction_execute');
    }

    public function itShouldOccurExceptionByWrongActionClassName()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'fuga';

        $e = null;
        try {
            $dispatcher->connect(':action')->dispatch($path);
        } catch (Net_URL_Dispatcher_Exception $ex) {
            $e = $ex;
        }

        $this->spec($e)->should->beAnInstanceOf('Net_URL_Dispatcher_Exception');
    }

    public function itShouldOccurExceptionByWrongParamter()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'fuga/hoge';

        $e = null;
        try {
            $dispatcher->connect(':action')->dispatch($path);
        } catch (Net_URL_Dispatcher_Exception $ex) {
            $e = $ex;
        }

        $this->spec($e)->should->beAnInstanceOf('Net_URL_Dispatcher_Exception');
    }

    public function itShouldDispatchToActionClassWithoutConnectMethod()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);

        ob_start();
        $dispatcher->dispatchAction('HogeAction', null, $path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('HogeAction_execute');
    }

    public function itShouldDispatchToActionClassAndRunPreExecuteMethod()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'pre';

        ob_start();
        $dispatcher->connect(':action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('PreAction_preExecute');
    }

    public function itShouldDispatchToActionClassAndRunPostExecuteMethod()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'post/';

        ob_start();
        $dispatcher->connect(':action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('PostAction_postExecute');
    }

    public function itShouldDispatchToActionClassWithPathinfoParameter()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'param/foo/bar';

        ob_start();
        $dispatcher->connect(':action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('bar');
    }

    public function itShouldDispatchToActionClassWithGetParameter()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'param';
        $_GET['foo'] = 'hoge';

        ob_start();
        $dispatcher->connect(':action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('hoge');
        unset($_GET);
    }

    public function itShouldDispatchToActionClassWithPostParamter()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'param';
        $_POST['foo'] = 'baz';

        ob_start();
        $dispatcher->connect(':action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('baz');
        unset($_POST);
    }

    public function itShouldDispatchToActionClassWithPathinfoAndGetParamters()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'param/hoge/fuga';
        $_GET['foo'] = 'bar';

        ob_start();
        $dispatcher->connect(':action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('fugabar');
    }

    public function itShouldDispatchToActionClassWithPathinfoAndPostParamters()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'param/hoge/fuga';
        $_POST['foo'] = 'bar';

        ob_start();
        $dispatcher->connect(':action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('fugabar');
    }

    public function itShouldDispatchToActionClassWithGetAndPostParameters()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'param';
        $_GET['hoge'] = 'fuga';
        $_POST['foo'] = 'bar';

        ob_start();
        $dispatcher->connect(':action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('fugabar');
        unset($_GET);
        unset($_POST);
    }

    public function itShouldDispatchToActionClassWithPathinfoAndGetAndPostParameters()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'param/hoge/fuga';
        $_GET['foo']  = 'bar';
        $_POST['baz'] = 'hoge';

        ob_start();
        $dispatcher->connect(':action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('fugabarhoge');
        unset($_GET);
        unset($_POST);
    }

    public function itShouldDispatchToActionClassAndCanUseActionStackMethod()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'stack1';

        ob_start();
        $dispatcher->connect(':action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('Stack2Action_execute');
    }

    public function itShouldDispatchToActionClassAndCanUseActionStackMethodWithParamter()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'stack3';

        ob_start();
        $dispatcher->connect(':action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('bar');
    }

    public function itShouldDispatchToActionClassAndCanUseActionStackMethodWithMetgedParameters()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'stack5/hoge/fuga';

        ob_start();
        $dispatcher->connect(':action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('fuga_bar');
    }

    public function itShouldDispatchToActionClassAndGetDefaultParam()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'defaultparam';

        ob_start();
        $dispatcher->connect(':action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('bar');
    }
}
