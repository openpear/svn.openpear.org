<?php
/**
 * Simple dispatcher class
 *
 * PHP version 5.2
 *
 * Copyright (c) 2009 Heavens hell, All rights reserved.
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
 *   * Neither the name of Heavens hell nor the names of his
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
 * @copyright 2009 Heavens hell
 * @author    Heavens hell <heavenshell.jp@gmail.com>
 * @license   New BSD License
 */

/**
 * @see prepare
 */
require_once dirname(dirname(__FILE__)) . '/prepare.php';

/**
 * DescribeDispatchToController
 *
 * @category  Net
 * @package   Net_URL_Dispatcher
 * @version   $id$
 * @copyright 2009 Heavens Hell
 * @author    Heavens hell <heavenshell.jp@gmail.com>
 * @license   New BSD License
 */
class DescribeControllerDispatcher extends SpecCommon
{
    public function before()
    {
    }

    public function after()
    {
    }

    public function itShouldDispatchToControllerClassByServerEnv()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = '/hoge/index';

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('HogeController_indexAction');
    }

    public function itShouldDispatchToControllerClassBySetterMethod()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $dispatcher->setPathInfo('hoge/index');

        ob_start();
        $dispatcher->connect(':controller/:action/')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('HogeController_indexAction');
    }

    public function itShouldDispatchToControllerClassWhichCallsStatic()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $dispatcher->setPathInfo('hoge/index');

        ob_start();
        $dispatcher->connect('hoge/:action/')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('HogeController_indexAction');
    }

    public function itShouldDispatchToControllerClassByServerEnvWhichCallStatic()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/index';

        ob_start();
        $dispatcher->connect('hoge/:action/')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('HogeController_indexAction');
    }

    public function itShouldDispatchToControllerClassByDefaultAction()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge';

        ob_start();
        $dispatcher->connect(':controller/:action/')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('HogeController_indexAction');
    }

    public function itShouldOccurExceptionByWrongControllerClassName()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'fuga';

        $e = null;
        try {
            $dispatcher->connect(':controller/:action/')->dispatch($path);
        } catch (Net_URL_Dispatcher_Exception $ex) {
            $e = $ex;
        }

        $this->spec($e)->should->beAnInstanceOf('Net_URL_Dispatcher_Exception');
    }

    public function itShouldOccurExceptionByWrongActionMethodName()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/fuga';

        $e = null;
        try {
            $dispatcher->connect(':controller/:action/')->dispatch($path);
        } catch (Net_URL_Dispatcher_Exception $ex) {
            $e = $ex;
        }

        $this->spec($e)->should->beAnInstanceOf('Net_URL_Dispatcher_Exception');
    }

    public function itShouldOccurExceptionByWrongParamter()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/index/foo/bar';

        $e = null;
        try {
            $dispatcher->connect(':controller/:action/id')->dispatch($path);
        } catch (Net_URL_Dispatcher_Exception $ex) {
            $e = $ex;
        }
        $this->spec($e)->should->beAnInstanceOf('Net_URL_Dispatcher_Exception');
    }


    public function itShouldDispatchToControllerClassWithoutConnectMethod()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);

        ob_start();
        $dispatcher->dispatchController('HogeController', 'indexAction', null, $path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('HogeController_indexAction');
    }

    public function itShouldDispatchToControllerClassAndRunPredispatchMethod()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'pre/index';

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('PreController_preDispatch');
    }

    public function itShouldDispatchToControllerClassAndRunPostdispatchMedhod()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'post/index';

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('PostController_postDispatch');
    }

    public function itShouldDispatchToControllerClassWithPathinfoParameter()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/param/foo/bar';

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('bar');
    }

    public function itShouldDispatchToControllerClassWithGetParameter()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/get';
        $_GET['bar'] = 'foo';

        ob_start();
        $dispatcher->connect(':controller/:action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('foo');
        unset($_GET);
    }

    public function itShouldDispatchToControllerClassWithPostParamter()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/post';
        $_POST['fuga'] = 'hoge';

        ob_start();
        $dispatcher->connect(':controller/:action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('hoge');
        unset($_POST);
    }

    public function itShouldDispatchToControllerClassWithPathinfoAndGetParamters()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/mix/hoge/fuga';
        $_GET['foo'] = 'bar';

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('fuga_bar');
        unset($_GET);
    }

    public function itShouldDispatchToControllerClassWithPathinfoAndPostParamters()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/mix/hoge/fuga';
        $_POST['foo'] = 'bar';

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('fuga_bar');
        unset($_POST);
    }

    public function itShouldDispatchToControllerClassWithGetAndPostParameters()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/mix/';
        $_GET['hoge'] = 'fuga';
        $_POST['foo'] = 'bar';

        ob_start();
        $dispatcher->connect(':controller/:action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('fuga_bar');
        unset($_GET);
        unset($_POST);
    }

    public function itShouldDispatchToControllerClassWithPathinfoAndGetAndPostParameters()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/pathinfogetpost/hoge/fuga';
        $_GET['foo']  = 'bar';
        $_POST['baz'] = 'hoge';

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('fuga_bar_hoge');
        unset($_GET);
        unset($_POST);
    }

    public function itShouldDispatchToControllerClassAndCanUseActionStackToSameClass()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/stack1';

        ob_start();
        $dispatcher->connect(':controller/:action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('HogeController_stack2Action');
    }

    public function itShouldDispatchToControllerClassAndCanUseActionStackToSameCalssWithParameter()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/stack3';

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('bar');
    }

    public function itShouldDispatchToControllerClassAndCanUseActionStackToOtherClass()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/stack5';

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('OtherController_indexAction');
    }

    public function itShouldDispatchToControllerClassAndCanUseActionStackToOtherClassWithParameter()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/stack6';

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('foo');
    }

    public function itShouldDispatchToControllerClassAndGetDefaultParam()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/getdefaultparam';

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->spec($buffer)->should->be('fuga');
    }
}
