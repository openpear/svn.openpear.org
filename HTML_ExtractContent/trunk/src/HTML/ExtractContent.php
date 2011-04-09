<?php


/**
 * HTML_ExtractContent
 *
 * extract the text from web page(html).
 *
 * fork from "Extract Content Module for html"
 * @link http://www.systemfriend.co.jp/node/326
 * @link http://hakaselab.sakura.ne.jp/make/extractcontent/extractcontent.phps
 * PHP-version author is Junichi Takahashi
 * 
 *
 * @license BSD
 */

class HTML_ExtractContent
{
    /**
     * optional array, set using ::setOpt()
     *
     * @var opt
     */ 
    protected $opt = array('threshold'  => 100, //threhold for score of cluster
                           'min_length' => 80,  // minimum length of blocks
                           'decay_factor' => 0.73, //decay factor for block scores
                           'continuous_factor' => 1.62,
                           'punctuation_weight' => 10,
                           'punctuations' =>  '/([、。，．！？]|\.[^A-Za-z0-9]|,[^0-9]|!|\?)/',
                           'waste_expressions' => '/Copyright|All Rights Reserved/i',
                           'debug'=> false
                           );
    /**
     * Analyses the given HTML text, extracts body and title  
     *
     * @param string $html
     * @param array $opt
     * @return 
     */
    public function analyze($html, $opt = array())
    {
        //frameset or redirect
        if (preg_match('/<\/frameset>|<meta\s+http-equiv\s*=\s*[\"\']?refresh[\'\"]?[^>]*url/i', $html)) {
            return array('', $this->title($html));
        }

        $this->setOpt($opt);

        // header & title
        if (0 < count($matches = preg_split('/<\/head\s*>/im', $html))) {
            $html = $matches['1'];
            $title = $this->title($matches[0]);
        } else {
            $title = $this->title($html);
        }

        // Google AdSense Section Target       
        $html = $this->section($html);
        
        //eliminate useles text
        $html = $this->eliminateUselessTags($html);
        
        //$this->hBlock
        
        //extract text blocks
        $factor = $continuous = 1.0;
        $body = '';
        $score = 0;
        $bodylist = array();
        $list = preg_split('/<\/?(?:div|center|td)[^>]*>|<p\s*[^>]*class\s*=\s*[\"\']?(?:posted|plugin-\w+)[\'\"]?[^>]*>/', $html);

        for($i = 0; $i < count($list); $i++) {
            if (!$list[$i]) continue;
            $list[$i] = trim($list[$i]);
            if ($this->hasOnlyTags($list[$i]))  continue;

            if (0 < strlen($body)) {
                $continuous /= $this->opt['continuous_factor'];
            }

            $notlinked = $this->eliminateLink($list[$i]);

            if (strlen($notlinked) < $this->opt['min_length']) continue;
            
            $c = (strlen($notlinked) + count(preg_split($this->opt['punctuations'], $notlinked)) * $this->opt['punctuation_weight']) * $factor;
            $factor *= $this->opt['decay_factor'];
            $not_body_rate = count(preg_split($this->opt['waste_expressions'], $list[$i])) + count(preg_split('/amazon[a-z0-9\.\/\-\?&]+-22/i', $list[$i])) / 2.0;
            $c *= ($not_body_rate > 0) ? pow(0.72, $not_body_rate) : 1;
            $c1 = $c * $continuous;

            if($c1 > $this->opt['threshold']) {
                $body  .= $list[$i] . "\n";
                $score += $c1;
                $continuous = $this->opt['continuous_factor'];
            } else if($c > $this->opt['threshold']) {
                $bodylist[] = array($body, $score);
                $body       = $list[$i] + "\n";
                $score      = $c;
                $continuous = $this->opt['continuous_factor'];
            }
        }

        $bodylist[] = array($body, $score);
        usort($bodylist, array($this,'bdSort'));
        return array($this->stripTags($bodylist[0][0]), $title);
    }

    /**
     * Google AdSense Section Target
     *
     * @param string $html
     * @param string $sectionStylye
     */
    protected function section($html, $sectionStylye = 'googlead')
    {
        $html = preg_replace('/<!--\s*google_ad_section_start\(weight=ignore\)\s*-->.*?<!--\s*google_ad_section_end.*?-->/ms', '', $html);
        
        if (preg_match('/<!--\s*google_ad_section_start[^>]*-->/', $html)) {
            preg_match_all('/<!--\s*google_ad_section_start[^>]*-->.*?<!--\s*google_ad_section_end.*?-->/ms', $html, $matches);
            $html = implode("\n", $matches[0]);
        }

        return $html;
    }

    /**
     * extract title
     *
     * @param string
     * @return string
     */
    public function title($html)
    {
        if (preg_match('/<title[^>]*>\s*(.*?)\s*<\/title\s*>/i', $html, $matches)) {
            return $this->stripTags($matches[1]);
        } else {
            return '';
        }
    
    }

    /**
     *
     */
    protected function bdSort($a,$b) {
        if ($a[1] == $b[1]) {
            return 0;
        }
        return $a[1] < $b[1] ? 1 : -1;
    }

    protected function eliminateUselessTags($html)
    {
        $html = preg_replace('/\342(?:\200[\230-\235]|\206[\220-\223]|\226[\240-\275]|\227[\206-\257]|\230[\205\206])/', '', $html);
        $html = preg_replace('/<(script|style|select|noscript)[^>]*>.*?<\/\1\s*>/ims', '', $html);
        $html = preg_replace('/<!--.*?-->/ms', '', $html);
        $html = preg_replace('/<![A-Za-z].*?>/', '', $html);
        $html = preg_replace('/<div\s[^>]*class\s*=\s*[\'\"]?alpslab-slide[\"\']?[^>]*>.*?<\/div\s*>/ms', '', $html);
        $html = preg_replace('/<div\s[^>]*(id|class)\s*=\s*[\'\"]?\S*more\S*[\"\']?[^>]*>/i', '', $html);
        
        return $html;
    
    }

    protected function hasOnlyTags($text)
    {
        $str = preg_replace('/<[^>]*>/ims', '', $text);
        $str = str_replace('&nbsp;', '', $str);
        return strlen(trim($str)) == 0;
    
    } 

    protected function eliminateLink($html)
    {
        $count = 0;
        $notlinked = preg_replace('/<a\s[^>]*>.*?<\/a\s*>/ims', '', $html, -1, $count);
        $notlinked = preg_replace('/<form\s[^>]*>.*?<\/form\s*>/ims', '', $html);
        $notlinked = $this->stripTags($notlinked);
        if (strlen($notlinked) < 20 * $count || $this->isLinkList($html)) {
            return "";
        } else {
            return $notlinked;
        }
    }

    protected function isLinkList($text)
    {
        if (preg_match('/<(?:ul|dl|ol)(.+?)<\/(?:ul|dl|ol)>/ims', $text, $matches)) {
            $listpart = $matches[1];
            $str      = preg_replace('/<(?:ul|dl)(.+?)<\/(?:ul|dl)>/ims', '', $text);
            $str      = preg_replace('/<.+?>/ms', '', $str);
            $str      = preg_replace('/\s+/', ' ', $str);
            $outside  = $str;
            $list     = preg_split('/<li[^>]*>/', $listpart);
            array_shift($list);
            $rate     = $this->evaluateList($list);
            return strlen($outside) <= strlen($text) / (45 / $rate);
        }

      return 0;
    }

    protected function evaluateList($list)
    {
        if (count($list) <= 0) return 1;
        $h = 0;
        for($i = 0; $i < count($list); $i++) {
            $h += preg_match('/<a\s+href=([\'\"]?)([^\"\'\s]+)\1/ims', $list[$i]);
        }
    
        return 9 * pow((1.0 * $h / count($list)), 2) + 1;
    } 

    protected function stripTags($html)
    {
        $str = preg_replace('/<.+?>/', '', $html);
        $str = mb_convert_kana($str, 'asK');
        $str = htmlspecialchars_decode($str);
    
        return $str;
    }


    /**
     * Set option parameters
     *
     * @param array $opt
     */
    public function setOpt(array $opt)
    {
       foreach($opt as $k => $v) $this->opt[$k] = $v;
    }
}

