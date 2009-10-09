<?php
/**
 * VirtualGirlFriend.php
 *
 */

/**
 * Wozozo_VirtualGirlFriend
 *
 */
class Wozozo_VirtualGirlFriend
{
    public $url = 'http://4u.straightline.jp/rss';

    /**
     * __construct
     *
     */
    public function __construct()
    {
    }

    /**
     * find
     *
     */
    public function find()
    {
        $items = $this->scraping();

        $key = array_rand($items);

        return $items[$key];
    }

    /**
     * scraping
     *
     */
    protected function scraping()
    {
        $atom = file_get_contents($this->url);
        $pattern = '_<media:thumbnail url="(.*?)" />_';
        preg_match_all($pattern, $atom, $matches);

        return $matches[1];
    }
}

/*
$wozozo = new Wozozo_VirtualGirlFriend();
$girlfriend_image = $wozozo->find();

echo("<img src=\"{$girlfriend_image}\" />");
 */
