<?php
/**
 * FindJob.php
 *
 */

/**
 * Wozozo_FindJob
 *
 */
class Wozozo_FindJob
{
    public $url ='http://www.find-job.net/fj/newjob_rss.pl?keyword=PHP';

    /**
     * __construct
     *
     */
    public function __construct()
    {
    }

    /**
     * getJob
     *
     */
    public function getJob()
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
        $xml = file_get_contents($this->url);
        $simplexml = simplexml_load_string($xml);

        $children = $simplexml->children();

        $items = array();
        foreach ($children->item as $child) {
            $item = array();
            foreach ($child as $key => $value) {
                $item[$key] = (string)$value;
            }
            $items[] = $item;
        }

        return $items;
    }
}

/*
$wozozo = new Wozozo_FindJob();
$job = $wozozo->getJob();
var_dump($job);
 */
