<?php
/**
 * <kbd>DummyTest.php</kbd>
 *
 * This file is part of fwComponents.
 *
 * fwComponents is free software: you can redistribute it and/or modify it under the
 * terms of the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * fwComponents is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE.  See the GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with fwComponents.  If not, see http://gnu.org/licenses/lgpl.txt.
 *
 * PHP version 5.3
 *
 * @category  Component
 * @package   Component
 * @author    Florian Wolters <wolters.florian@gmx.net>
 * @copyright 2011 Florian Wolters
 * @license   http://gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @version   SVN: $Id$
 * @link      http://github.com/tehhahn/fwComponents
 * @since     File available since Release 1.0.0
 */

declare(encoding='utf-8');

namespace fw\Component;

require_once __DIR__ . '/../../../../php/fw/Component/Dummy.php';
require_once __DIR__ . '/../../../../php/fw/Component/DummyException.php';

/**
 * Test class for <tt>{@link Dummy}</tt>.
 *
 * Generated by PHPUnit on 2011-11-08 at 20:42:52.
 *
 * @category  Component
 * @package   Component
 * @author    Florian Wolters <wolters.florian@gmx.net>
 * @copyright 2011 Florian Wolters
 * @license   http://gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @version   Release: @package_version@
 * @link      http://github.com/tehhahn/fwComponents
 * @link      Dummy
 * @since     Class available since Release 1.0.0
 */
class DummyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The <tt>{@link Dummy}</tt> object under test.
     *
     * @var Dummy
     */
    private $_oDummy;

    /**
     * Sets up the fixture.
     *
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->_oDummy = new Dummy('foo');
    }

    /**
     * Tests <tt>{@link Dummy::__construct()}</tt>.
     *
     * @covers fw\Component\Dummy::__construct
     *
     * @return void
     * @testdox __construct returns instance of fw\Component\Dummy
     */
    public function testConstructorReturnsInstanceOfDummy()
    {
        $this->assertInstanceOf(__NAMESPACE__ . '\Dummy', new Dummy('test'));
    }

    /**
     * Tests whether <tt>{@link Dummy::__construct()}</tt> throws an <tt>{@link
     * \InvalidArgumentException}</tt> if the first argument is not a
     * <tt>string</tt>.
     *
     * @return void
     * @covers fw\Component\Dummy::__construct
     * @expectedException fw\Component\DummyException
     * @expectedExceptionMessage The first argument has to be of type string.
     * @testdox __construct throws fw\Component\DummyException if the first argument is not a string
     */
    public function testConstructorThrowsDummyExceptionIfFirstArgumentIsNotString()
    {
        new Dummy(null);
    }

    /**
     * Tests <tt>{@link Dummy::__toString()}</tt>.
     *
     * @covers fw\Component\Dummy::__toString
     *
     * @return void
     * @testdox __toString returns correct value
     */
    public function testMagicToStringReturnsCorrectValue()
    {
        $this->assertEquals('foo', $this->_oDummy->__toString());
    }

}
