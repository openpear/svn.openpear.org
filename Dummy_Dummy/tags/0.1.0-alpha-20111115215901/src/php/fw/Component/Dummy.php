<?php
/**
 * <kbd>Dummy.php</kbd>
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

/**
 * A dummy class.
 *
 * @category  Component
 * @package   Component
 * @author    Florian Wolters <wolters.florian@gmx.net>
 * @copyright 2011 Florian Wolters
 * @license   http://gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @version   Release: @package_version@
 * @link      http://github.com/tehhahn/fwComponents
 * @since     Class available since Release 1.0.0
 */
final class Dummy
{

    /**
     * The <tt>string</tt> of this dummy.
     *
     * @var string
     */
    private $_sStr;

    /**
     * Creates a new dummy.
     *
     * @param string $sStr The <tt>string</tt> of this dummy.
     *
     * @throws DummyException If the <var>$sStr</var> argument is not a
     *                        <tt>string</tt>.
     */
    public function __construct($sStr)
    {
        if ( !is_string($sStr) ) {
            throw new DummyException('The first argument has to be of type string.');
        }
        $this->_sStr = $sStr;
    }

    /**
     * Returns the string representation of this dummy.
     *
     * @return string The string representation.
     */
    public function __toString()
    {
        return $this->_sStr;
    }

}
