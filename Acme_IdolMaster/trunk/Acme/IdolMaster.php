<?php
/**
 * Acme_IdolMaster
 *
 * @author  yohei.kawano@gmail.com
 * @package openpear
 * @version $Id$
 */

class Acme_IdolMaster implements Iterator
{

    /**
     * Array for Object
     * @var    array
     * @access public
     */
    public $members = array();

    /**
     * Members list
     * @var    array
     * @access private
     */
    private $memberNames = array(
        'AmamiHaruka',
        'KisaragiChihaya',
        'HagiwaraYukiho',
        'TakatsukiYayoi',
        'AkizukiRitsuko',
        'MiuraAzusa',
        'MinaseIori',
        'KikuchiMakoto',
        'FutamiAmi',
        'FutamiMami',
        'HoshiiMiki',
    );

    /**
     * Iterator position
     * @var    integer
     * @access private
     */
    private $position = 0;

    /**
     * @var    boolean
     * @access private
     */
    private $current_member = false;

    /**
     * Constructor
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        foreach ($this->memberNames as $member) {
            require_once 'Acme/IdolMaster/Member/' . $member . '.php';
            $class = 'Acme_IdolMaster_Member_'. $member;
            $this->members[$member] = new $class();
        }
    }


    //メンバーを指定
    /**
     * Select member
     *
     * @param  boolean   $memberName
     * @return boolean
     * @access public
     * @throws Exception
     */
    public function select($memberName = false)
    {
        if (isset($this->members[$memberName])) {
            $this->current_member = $memberName;
            return true;
        }
        elseif ($memberName == false) {
            $this->current_member = false;
        }
        else{
            throw new Exception('member not found');
        }
    }

    //マジックメソッド__get
    /**
     *
     * @param  string    $memberName
     * @return Object
     * @access public
     */
    public function __get($memberName)
    {
        return $this->get($memberName);
    }

    //メンバーを取得    メンバー指定後であれば要素を取得
    /**
     *
     * @param  boolean   $memberName
     * @return mixed
     * @access public
     * @throws Exception
     */
    public function get($memberName = false)
    {
        //メンバー指定があれば$memberNameのかわりに$propertyが来る
        if ($this->current_member) {
            if ($memberName) {
                return $this->members[$this->current_member]->{$memberName};
            }
            else {
                return $this->members[$this->current_member];
            }
        }
        //引数のメンバー取得
        if (isset($this->members[$memberName])) {
            return $this->members[$memberName];
        } else {
            throw new Exception('member not found');
        }
    }

    //マジックメソッド__set
    /**
     * @param  mixed    $property
     * @param  mixed    $value
     * @access public
     */
    public function __set($property, $value)
    {
        return $this->set($property, $value);
    }

    // 値ゲット
    /**
     * @param  mixed    $property
     * @param  mixed    $value
     * @return mixed
     * @access public
     */
    public function set($property, $value)
    {
        //メンバそのものは追記不可
        if (!$this->current_member) {
            return false;
        }

        //セット
        return $this->members[$this->current_member]->set($property, $value);
    }

    //マジックメソッド__call
    /**
     * @param  string    $method
     * @param  array    $args
     * @return mixed
     * @access public
     */
    function __call($method, $args)
    {
        if (empty($args)) {
            return $this->get($method);
        }
        elseif (isset($args[0])) {
            return $this->set($method, $args[0]);
        }

        return null;
    }

    //イテレータ
    /**
     * Iterator::current
     *
     * @return mixed
     * @access public
     */
    public function current()
    {
        return $this->members[$this->memberNames[$this->position]];
    }

    /**
     * Iterator::valid
     *
     * @return boolean
     * @access public
     */
    public function valid()
    {
        return isset($this->members[$this->memberNames[$this->position]]);
    }

    /**
     * Iterator::key
     *
     * @return integer
     * @access public
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Iterator::next
     *
     * @return void
     * @access public
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Iterator::rewind
     *
     * @return void
     * @access public
     */
    public function rewind()
    {
        $this->position = 0;
    }

}
