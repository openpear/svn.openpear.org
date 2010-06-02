<?php
// Based on: http://github.com/cho45/net-irc
require_once 'Net/IRC.php';

class Net_IRC_Pattern
{
    static private $_is_init_ = false;

    static public $LETTER = 'A-Za-z';
    static public $DIGIT = '0-9';
    static public $HEXDIGIT = '0-9A-Fa-f';
    static public $SPECIAL = '\x5B-\x60\x7B-\x7D';

    static public $SHORTNAME;
    static public $HOSTNAME;
    static public $SERVERNAME;

    static public $NICKNAME = '\S+';
    static public $USER = '[\x01-\x09\x0B-\x0C\x0E-\x1F\x21-\x3F\x41-\xFF]+';

    static public $IP4ADDR = '[0-9]{1,3}(?:\\.[0-9]{1,3}){3}';
    static public $IP6ADDR;
    static public $HOSTADDR;

    static public $HOST;

    static public $PREFIX;

    static public $NOSPCRLFCL = '\x01-\x09\x0B-\x0C\x0E-\x1F\x21-\x39\x3B-\xFF';
    static public $COMMAND;

    static public $MIDDLE;
    static public $TRAILING;
    static public $PARAMS;

    static public $CRLF = '\x0D\x0A';
    static public $MESSAGE;

    static public $CLIENT_PATTERN;
    static public $MESSAGE_PATTERN;

    static public function init() {
        if (self::$_is_init_ === true) {
            return;
        }

        self::$SHORTNAME = sprintf('[%s%s](?:[-%1$s%2$s]*[%1$s%2$s])?', self::$LETTER, self::$DIGIT);
        self::$HOSTNAME = sprintf('%s(?:\.%1$s)*', self::$SHORTNAME);
        self::$SERVERNAME = self::$HOSTNAME;

        self::$IP6ADDR = sprintf('(?:[%1$s]+(?::[%1$s]+){7}|0:0:0:0:0:(?:0|FFFF):%2$s)', self::$HEXDIGIT, self::$IP4ADDR);
        self::$HOSTADDR = sprintf('(?:%s|%s)', self::$IP4ADDR, self::$IP6ADDR);
        
        self::$HOST = sprintf('(?:%s|%s)', self::$HOSTNAME, self::$HOSTADDR);

        self::$PREFIX = sprintf('(?:%s(?:(?:!%s)?@%s)?|%s)', self::$NICKNAME, self::$USER, self::$HOST, self::$SERVERNAME);
        self::$COMMAND = sprintf('(?:[%s]+|[%s]{3})', self::$LETTER, self::$DIGIT);

        self::$MIDDLE = sprintf('[%1$s][:%1$s]*', self::$NOSPCRLFCL);
        self::$TRAILING = sprintf('[: %s]*', self::$NOSPCRLFCL);
        self::$PARAMS = sprintf('(?:((?: %1$s){0,14})(?: :(%2$s))?|((?: %1$s){14}):?(%2$s))', self::$MIDDLE, self::$TRAILING);

        self::$MESSAGE = sprintf('(?::(%s) )?(%s)%s\s*%s', self::$PREFIX, self::$COMMAND, self::$PARAMS, self::$CRLF);

        self::$_is_init_ = true;
    }

    static public function message_pattern() {
        return '/\A'. self::$MESSAGE. '\z/S';
    }

    static public function p($name) {
        if (isset(self::$$name)) {
            return '/'. self::$$name. '/u';
        }
        throw new Net_IRC_Exception($name. ' is not defined pattern');
    }
}
Net_IRC_Pattern::init();

