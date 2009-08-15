<?php
/**
 * Spec for HogeController
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
 * @see Net_URL_Dispatcher_Controller
 */
require_once 'Net/URL/Dispatcher/Controller.php';

/**
 * HogeController
 *
 * @category  Net
 * @package   Net_URL_Dispatcher
 * @version   $id$
 * @copyright 2009 Heavens hell
 * @author    Heavens hell <heavenshell.jp@gmail.com>
 * @license   New BSD License
 */
class HogeController extends Net_URL_Dispatcher_Controller
{
    public function preDispatch()
    {
    }

    public function indexAction()
    {
        echo __CLASS__ . '_' .  __FUNCTION__;
    }

    public function hogeAction()
    {
        echo __CLASS__ . '_' .  __FUNCTION__;
    }

    public function postDispatch()
    {
    }

    public function paramAction()
    {
        echo $this->getParam('foo');
    }

    public function getAction()
    {
        echo $this->getParam('bar');
    }

    public function postAction()
    {
        echo $this->getParam('fuga');
    }

    public function mixAction()
    {
        $params = $this->getParam('hoge') . '_' . $this->getParam('foo');
        echo $params;
    }

    public function pathinfogetpostAction()
    {
        $params = $this->getParam('hoge') . '_' . $this->getParam('foo') . '_' . $this->getParam('baz');
        echo $params;
    }

    public function stack1Action()
    {
        $this->actionStack('stack2');
    }

    public function stack2Action()
    {
        echo __CLASS__ . '_' . __FUNCTION__;
    }

    public function stack3Action()
    {
        $this->actionStack('stack4', null, array('foo' => 'bar'));
    }

    public function stack4Action()
    {
        echo $this->getParam('foo');
    }

    public function stack5Action()
    {
        $this->actionStack('index', 'Other');
    }

    public function stack6Action()
    {
        $this->actionStack('stack1', 'Other', array('bar' => 'foo'));
    }

    public function getdefaultparamAction()
    {
        echo $this->getParam('hoge', 'fuga');
    }
}
