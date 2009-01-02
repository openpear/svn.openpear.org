<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Services_Google_Calendar
 *
 * A PHP implementation of the Google Calendar Data API
 * (http://code.google.com/apis/gdata/calendar.html)
 *
 * PHP versions 4 and 5
 *
 * @category  Services
 * @package   Services_Google_Calendar
 * @author    Keigo AOKI <hoyo1111 at gmail dot com>
 * @copyright 2006-2009 Keigo AOKI
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   Release: 0.1.0
 * @link      http://openpear.org/package/Services_Google_Calendar
 */

/**
 * uses PEAR error management
 */
require_once 'PEAR.php';

/**
 * uses HTTP_Client for GData authentication
 */
require_once 'HTTP/Client.php';

/**
 * uses XML_Serializer for build and parse atom feed
 */
require_once 'XML/Serializer.php';
require_once 'XML/Unserializer.php';


/**
 * end point of gdata api
 */
define('SERVICES_GOOGLE_CALENDAR_URL_AUTH', 'https://www.google.com/accounts/ClientLogin');
define('SERVICES_GOOGLE_CALENDAR_URL_FEED', 'http://www.google.com/calendar/feeds/');
define('SERVICES_GOOGLE_CALENDAR_URL_POST', 'http://www.google.com/calendar/feeds/default/private/full');

/**
 * user agent of this class
 */
define('SERVICES_GOOGLE_CALENDAR_SOURCE', 'openpear/Services_Google_Calendar');


/**
 * Services_Google_Calendar
 *
 * usage sample to get events.
 *
 * <code>
 * require_once 'Services/Google/Calendar.php';
 *
 * // initialize
 * $gc = new Services_Google_Calendar();
 *
 * // get events data
 * $public_data = $gc->getEvents($gmail_id);
 *
 * // get events data (private mode)
 * $private_data = $gc->getEvents($gmail_id, $hash);
 * </code>
 *
 *
 * usage sample to add an event.
 *
 * <code>
 * require_once 'Services/Google/Calendar.php';
 *
 * // initialize
 * $gc = new Services_Google_Calendar($gmail_id, $passwd);
 *
 * // set event data
 * $entry['title']        = 'Event title';
 * $entry['content']      = 'Event description';
 * $entry['where']        = 'Where the event helds';
 * $entry['when'][0]      = '2006-10-20';
 * $entry['when'][1]      = '2006-10-24';
 * $entry['transparency'] = 'transparent';
 * $entry['visibility']   = 'private';
 *
 * // add an event
 * $result = $gc->addEvent($entry);
 * </code>
 *
 *
 * @category  Services
 * @package   Services_Google_Calendar
 * @author    Keigo AOKI <hoyo1111 at gmail dot com>
 * @copyright 2006-2009 Keigo AOKI
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   Release: 0.1.0
 * @link      http://openpear.org/package/Services_Google_Calendar
 */
class Services_Google_Calendar
{
    /**
     * list of all options
     *
     * @access  private
     * @var     array
     */
    var $_options = array(
              'Email'   => '',
              'Passwd'  => '',
              'source'  => SERVICES_GOOGLE_CALENDAR_SOURCE,
              'service' => 'cl',
            );


    /**
     * instance of HTTP_Client
     *
     * @access  private
     * @var     object
     */
    var $_client;

    /**
     * authentication token for header part of post request
     *
     * @access  private
     * @var     string
     */
    var $_authToken = '';

    /**
     * constructor
     *
     * @access  public
     * @param   string $email   email address of Google account
     * @param   string $passwd  password of Google account
     */
    function Services_Google_Calendar($email = null, $passwd = null)
    {
        if (!empty($email)) {
            $this->_options['Email']  = $email;
        }
        if (!empty($passwd)) {
            $this->_options['Passwd'] = $passwd;
        }
        $this->_client =& new HTTP_Client(array('timeout' => 10));
        if (!empty($email) && !empty($passwd)) {
            $this->_authentication();
        }
    }

    /**
     * set an option
     *
     * @access  public
     * @param   string $key     key of option (email, passwd, source)
     * @param   string $value   value of option
     */
    function setOption($key, $value)
    {
        $this->_options[$key] = $value;
    }

    /**
     * get a list of the user's calendars
     *
     * @access  public
     * @return  string  XML feed of a list of calendars (meta-feed)
     * @throws  PEAR_Error
     */
    function getCalendars()
    {
        if ($this->_authToken === '') {
            if (!$authResult = $this->_authentication()) {
                return $authResult;
            }
        }
        if (!PEAR::isError($this->_client->get(SERVICES_GOOGLE_CALENDAR_URL_FEED . $this->_options['Email']))) {
            $result = $this->_client->currentResponse();
            return $this->_parseCalendars($result['body']);
        }
        return PEAR::raiseError('Connection failed of GET request');
    }

    /**
     * get a list of events
     *
     * @access  public
     * @param   string $email  Google account
     * @param   string $hash   magicCookie (to get private calendar)
     * @param   array  $range  date-range (0 => min('y', 'm', 'd'), 1 => max('y', 'm', 'd'))
     * @return  array          events information
     * @throws  PEAR_Error
     */
    function getEvents($email = '', $hash = '', $range = null)
    {
        if (empty($email)) {
            $email = $this->_options['Email'];
        }
        $mode = empty($hash) ? 'public' : 'private-' . $hash;
        $url = SERVICES_GOOGLE_CALENDAR_URL_FEED . $email . '/' . $mode . '/full?orderby=starttime';
        if (is_array($range)) {
            $min = date('Y-m-d', mktime(0, 0, 0, $range[0]['m'], $range[0]['d'], $range[0]['y']));;
            $max = date('Y-m-d', mktime(0, 0, 0, $range[1]['m'], $range[1]['d'], $range[1]['y']));;
            $data = array('start-min' => $min, 'start-max' => $max);
        }
        if (!PEAR::isError($this->_client->get($url, isset($data) ? $data : null))) {
            $result = $this->_client->currentResponse();
            return $this->_parseEvents($result['body']);
        }
        return PEAR::raiseError('Connection failed of GET request');
    }

    /**
     * get a event
     *
     * @access  public
     * @param   string $email  Google account
     * @param   string $hash   magicCookie (to get private calendar)
     * @param   string $id     event id
     * @return  array          a event information
     * @throws  PEAR_Error
     */
    function getEventById($email = '', $hash = '', $id)
    {
        if (empty($email)) {
            $email = $this->_options['Email'];
        }

        $mode = empty($hash) ? 'public' : 'private-' . $hash;
        $url = SERVICES_GOOGLE_CALENDAR_URL_FEED . $email . '/' . $mode . '/full/' . $id;

        if (!PEAR::isError($this->_client->get($url, null))) {
            $result = $this->_client->currentResponse();
            $data = $this->_parseEvents($result['body']);
            return $data[0];
        } else {
            return PEAR::raiseError('Connection failed of GET request');
        }
    }

    /**
     * add an event to user's calendar
     *
     * @access  public
     * @param   mix $entry  array or string (atom feeds) of event information
     * @return  string      event id of Google Calendar
     * @throws  PEAR_Error
     */
    function addEvent($entry)
    {
        if ($this->_authToken === '') {
            if (!$authResult = $this->_authentication()) {
                return $authResult;
            }
        }
        if (is_array($entry)) {
            $entry = $this->_buildAtom($entry);
        }
        $this->_client->setDefaultHeader('Content-type', 'application/atom+xml');
        $this->_client->setDefaultHeader('Content-length', strlen($entry));
        $this->_client->setMaxRedirects(0);
        if (!PEAR::isError($this->_client->post(SERVICES_GOOGLE_CALENDAR_URL_POST, $entry, true))) {
            $result = $this->_client->currentResponse();
            if ($result['code'] == 302) {
                if (!PEAR::isError($this->_client->post($result['headers']['location'], $entry, true))) {
                    $result = $this->_client->currentResponse();
                    if ($result['code'] == 201) {
                        $unserializer =& new XML_Unserializer();
                        if (!PEAR::isError($unserializer->unserialize($result['body']))) {
                            $xml = $unserializer->getUnserializedData();
                            return $this->getId($xml['id']);
                        }
                        return PEAR::raiseError('Atom Feed was not unserialized successfully');
                    }
                    return PEAR::raiseError($result['body']);
                }
            }
        }
        return PEAR::raiseError('Connection failed of POST request');
    }

    /**
     * edit an event on user's calendar
     *
     * @access  public
     * @param   string $id  event id
     * @param   mix $entry  array or string (atom feeds) of event information
     * @return  string      event id of Google Calendar
     * @throws  PEAR_Error
     */
    function editEvent($id, $entry)
    {
        if ($this->_authToken === '') {
            if (!$authResult = $this->_authentication()) {
                return $authResult;
            }
        }
        if (is_array($entry)) {
            $entry = $this->_buildAtom($entry);
        }
        if (PEAR::isError($url = $this->getEditUri($id))) {
            return $url;
        }
        $this->_client->setDefaultHeader('Content-type', 'application/atom+xml');
        $this->_client->setDefaultHeader('Content-length', strlen($entry));
        $this->_client->setDefaultHeader('X-Http-Method-Override', 'PUT');
        $this->_client->setMaxRedirects(0);
        if (!PEAR::isError($this->_client->post($url, $entry, true))) {
            $result = $this->_client->currentResponse();
            if ($result['code'] == 302) {
                if (!PEAR::isError($this->_client->post($result['headers']['location'], $entry, true))) {
                    $result = $this->_client->currentResponse();
                    if ($result['code'] == 200) {
                        $unserializer =& new XML_Unserializer();
                        if (!PEAR::isError($unserializer->unserialize($result['body']))) {
                            $xml = $unserializer->getUnserializedData();
                            return $this->getId($xml['id']);
                        }
                        return PEAR::raiseError('Atom Feed was not unserialized successfully');
                    }
                    return PEAR::raiseError($result['body']);
                }
            }
        }
        return PEAR::raiseError('Connection failed of EDIT request');
    }

    /**
     * remove an event from user's calendar
     *
     * @access  public
     * @param   string $id  event id
     * @return  void
     * @throws  PEAR_Error
     */
    function removeEvent($id)
    {
        if ($this->_authToken === '') {
            if (!$authResult = $this->_authentication()) {
                return $authResult;
            }
        }
        if (PEAR::isError($url = $this->getEditUri($id))) {
            return $url;
        }
        $entry = "test data";
        $this->_client->setDefaultHeader('Content-Length', strlen($entry));
        $this->_client->setDefaultHeader('X-Http-Method-Override', 'DELETE');
        $this->_client->setMaxRedirects(0);
        if (!PEAR::isError($this->_client->post($url, $entry, true))) {
            $result = $this->_client->currentResponse();
            if ($result['code'] == 302) {
                if (!PEAR::isError($this->_client->post($result['headers']['location'], $entry, true))) {
                    $result = $this->_client->currentResponse();
                    if ($result['code'] == 200) {
                        return null;
                    }
                    return PEAR::raiseError($result['body']);
                }
            }
        }
        return PEAR::raiseError('Connection failed of DELETE request');
    }

    /**
     * get edit URI for event id
     *
     * @access  public
     * @param   string $id  event id
     * @return  string      edit URI
     * @throws  PEAR_Error
     */
    function getEditUri($eid)
    {
        if ($this->_authToken === '') {
            if (!$authResult = $this->_authentication()) {
                return $authResult;
            }
        }
        $url = SERVICES_GOOGLE_CALENDAR_URL_POST . '/' . $eid;
        if (!PEAR::isError($this->_client->get($url))) {
            $result = $this->_client->currentResponse();
            if ($result['code'] == 200) {
                $unserializer =& new XML_Unserializer();
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
                if (!PEAR::isError($unserializer->unserialize($result['body']))) {
                    $xml = $unserializer->getUnserializedData();
                    for ($i = 0; $i < sizeof($xml['link']); $i++) {
                        if ($xml['link'][$i]['rel'] == 'edit') {
                            return $xml['link'][$i]['href'];
                        }
                    }
                }
                return PEAR::raiseError('Atom Feed was not unserialized successfully');
            }
            return PEAR::raiseError($result['body']);
        }
        return PEAR::raiseError('Connection failed of GET request');
    }

    /**
     * authentication to connect GData API
     *
     * @access  private
     * @param   string $key     key of option (email, passwd, source)
     * @param   string $value   value of option
     * @throws  PEAR_Error
     */
    function _authentication()
    {
        if (strlen($_SESSION['Services_Google_Calendar_Token']) > 0 && time() % 100 != 0) {
            $this->_authToken = $_SESSION['Services_Google_Calendar_Token'];
            //return true;
        }
        if (!PEAR::isError($this->_client->post(SERVICES_GOOGLE_CALENDAR_URL_AUTH, $this->_options))) {
            $result = $this->_client->currentResponse();
            if ($result['code'] == 200) {
                $this->_authToken = trim(substr(strstr($result['body'], 'Auth='), 5));
                $_SESSION['Services_Google_Calendar_Token'] = $this->_authToken;
                $this->_client->setDefaultHeader('Authorization', 'GoogleLogin auth=' . $this->_authToken);
                return true;
            }
        }
        return PEAR::raiseError('Authentication failed');
    }

    /**
     * parse events feed to array
     *
     * @access  private
     * @param   string $feed  atom feed of calendars list
     * @return  array         calendars information
     * @throws  PEAR_Error
     */
    function _parseCalendars($feed)
    {
        $unserializer =& new XML_Unserializer();
        if (!PEAR::isError($unserializer->unserialize($feed))) {
            $xml = $unserializer->getUnserializedData();
            $entries = isset($xml['entry'][0]) ? $xml['entry'] : array($xml['entry']);
            foreach ($entries as $i => $entry) {
                foreach ($entry as $key => $value) {
                    $calendars[$i]['id'] = $this->getId($entry['id']);
                    $calendars[$i]['title'] = !empty($entry['title']) ? $entry['title'] : '';
                    $calendars[$i]['summary'] = !empty($entry['summary']) ? $entry['summary'] : '';
                }
            }
            return $calendars;
        }
        return PEAR::raiseError('Parse error of calendar feed');
    }

    /**
     * parse events feed to array
     *
     * @access  private
     * @param   string $feed  atom feed of events list
     * @return  array         events information
     * @throws  PEAR_Error
     */
    function _parseEvents($feed)
    {
        $unserializer =& new XML_Unserializer();
        $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
        if (!PEAR::isError($unserializer->unserialize($feed))) {
            $xml = $unserializer->getUnserializedData();
            if (isset($xml['entry'][0])) {
                $entries = $xml['entry'];
            } else if (isset($xml['entry'])) {
                $entries = array($xml['entry']);
            } else {
                $entries = array($xml);
            }
            foreach ($entries as $i => $entry) {
                foreach ($entry as $key => $value) {
                    switch ($key) {
                    case 'id':
                        $events[$i]['id'] = $this->getId($value);
                        break;
                    case 'title':
                    case 'content':
                        $events[$i][$key] = !empty($value['_content']) ? $value['_content'] : '';
                        $events[$i][$key] = str_replace('{', '<', $events[$i][$key]);
                        $events[$i][$key] = str_replace('}', '>', $events[$i][$key]);
                        break;
/*
                    case 'author':
                        $events[$i]['name'] = $value['name'];
                        $events[$i]['email'] = $value['email'];
                        break;
*/
                    case 'gd:where':
                        $events[$i]['where'] = !empty($value['valueString']) ? $value['valueString'] : '';
                        break;
                    case 'gd:when':
                        $events[$i]['when'][0] = $this->_parseRfc3339($value['startTime']);
                        $events[$i]['when'][1] = $this->_parseRfc3339($value['endTime']);
                        if (strpos('T', $value['endTime']) === false) {
                            $events[$i]['when'][1] -= 86400;
                        }
                        break;
                    case 'gd:eventStatus':
                        $events[$i]['status'] = substr($value['value'], 39);
                        break;
                    }
                }
            }
            return $events;
        }
        return PEAR::raiseError('Parse error of calendar feed');
    }

    /**
     * convert RFC3339 timestamp to Unix time
     *
     * @access  private
     * @param   string $timestamp RFC3339 timestamp
     * @return  int               Unix time
     */
    function _parseRfc3339($timestamp)
    {
        preg_match('!^(\d{4})-(\d{2})-(\d{2})(T(\d{2}):(\d{2}):(\d{2}))?!', $timestamp, $match);
        if (!empty($match[4])) {
            return mktime($match[5], $match[6], $match[7], $match[2], $match[3], $match[1]);
        } else {
            return mktime(0, 0, 0, $match[2], $match[3], $match[1]);
        }
    }

    /**
     * convert event information to atom feed
     *
     * @access  private
     * @param   array $entry  array of event information
     * @return  string        atom feed of event information
     * @throws  PEAR_Error
     */
    function _buildAtom($entry)
    {
        $rootAttr['xmlns'] = 'http://www.w3.org/2005/Atom';
        $rootAttr['xmlns:gd'] = 'http://schemas.google.com/g/2005';
        $data['category']['_attr']['schema'] = 'http://schemas.google.com/g/2005#kind';
        $data['category']['_attr']['term'] = 'http://schemas.google.com/g/2005#event';
        foreach ($entry as $key => $value) {
            switch ($key) {
            case 'title':
            case 'content':
                $data[$key][] = $value;
                $data[$key]['_attr']['type'] = 'text';
                break;
/*
            case 'name':
            case 'email':
                $data['author'][$key] = $value;
                break;
*/
            case 'where':
                $data['gd:where']['_attr']['valueString'] = $value;
                break;
            case 'when':
                if (is_array($value) && count($value) > 0) {
                    $data['gd:when']['_attr']['startTime'] = $value[0];
                    if (count($value) > 1) {
                        $data['gd:when']['_attr']['endTime'] = $value[1];
                    }
                } else {
                    $data['gd:when']['_attr']['startTime'] = $value;
                }
                break;
            case 'eventStatus':
            case 'visibility':
            case 'transparency':
                $data['gd:' . $key]['_attr']['value'] = 'http://schemas.google.com/g/2005#event.' . $value;
                break;
            }
        }
        $serializer =& new XML_Serializer();
        $serializer->setOption(XML_SERIALIZER_OPTION_ROOT_NAME, 'entry');
        $serializer->setOption(XML_SERIALIZER_OPTION_ROOT_ATTRIBS, $rootAttr);
        $serializer->setOption(XML_SERIALIZER_OPTION_ATTRIBUTES_KEY, '_attr');
        $serializer->setOption(XML_SERIALIZER_OPTION_MODE, XML_SERIALIZER_MODE_SIMPLEXML);
        if (!PEAR::isError($serializer->serialize($data))) {
            return $serializer->getSerializedData();
        }
        return PEAR::raiseError('Entry was not serialized successfully');
    }

    /**
     * get id from url
     *
     * @access  private
     * @param   string $url  Google Calendar url
     * @return  string       id
     */
    function getId($url)
    {
        return substr(strrchr($url, '/'), 1);
    }
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
?>
