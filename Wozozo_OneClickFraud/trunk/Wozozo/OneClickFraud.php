<?php
/**
 *
 *
 */

/**
 * Wozozo_OneClickFraud
 *
 */
class Wozozo_OneClickFraud
{
    public function __construct()
    {
    }

    public function scrape()
    {
        $sites = array();
        $url  = 'http://www.ken-hokuto.com/oneclick/';
        $html = file_get_contents($url);

        $pattern = '_<div id="scrollbox">(.*?)</div>_sm';
        preg_match($pattern, $html, $matches);

        $table = mb_convert_encoding($matches[1], 'UTF-8', 'Shift_JIS');

        $pattern = '_<tr>(.*?)</tr>_sm';
        preg_match_all($pattern, $table, $matches, PREG_PATTERN_ORDER);

        foreach ($matches[1] as $tds) {
            $pattern = '_<td.*?>(.*?)</td>_sm';
            $result = preg_match_all($pattern, $tds, $items);
            if ($result === 0) {
                continue;
            }
            if (count($items[1]) == 1) {
                continue;
            }
            $site = array(
                'index'     => $items[1][0],
                'site_name' => $items[1][1],
                'site_url'  => $items[1][2],
                'note'      => $items[1][3],
            );
            $site = array_map('trim', $site);

            $sites[] = $site;
        }

        return $sites;
    }
}

// example
//
//$wozozo = new Wozozo_OneClickFraud();
//$sites = $wozozo->scrape();

//echo '<ul>';
//foreach ($sites as $site) {
//    echo "<li><a href=\"{$site['site_url']}\">{$site['site_name']}</a></li>";
//}
//echo '</ul>';
