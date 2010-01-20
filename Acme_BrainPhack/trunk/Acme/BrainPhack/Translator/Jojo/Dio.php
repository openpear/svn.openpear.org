<?php
/*
 *   Copyright (c) 2010 msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.*
 */

/**
 * @package Acme_BrainPhack_Translator_Jojo
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id$
 */

/**
 * JOJO's DIO mapping
 *
 * @author msakamoto-sf <sakamoto-gsyc-3s@glamenv-septzen.net>
 * @package Acme_BrainPhack_Translator_Jojo
 * @since 0.0.2
 */
class Acme_BrainPhack_Translator_Jojo_Dio
{
    // {{{ getMap()

    function getMap()
    {
        return array(
            '+' => array('����'),
            '-' => array('���_'),
            '>' => array('�`�`�`�`�`'), // KUAAAAAAAAAAAAA!!!!!!!
            '<' => array('�x�x�x�x�x'), // WRYYYYYYYYYYYYY!!!!!!!
            '.' => array('���[�h���[���[���b�I'),
            ',' => array('���O�͍��܂ŐH�����p���̖������o���Ă��邩�H'),
            '[' => array('����~�܂�u�U�E���[���h�v�I'),
            ']' => array('�����Ď��͓����o��'),
            );
    }

    // }}}
}

/**
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 * vim: set expandtab tabstop=4 shiftwidth=4:
 */
