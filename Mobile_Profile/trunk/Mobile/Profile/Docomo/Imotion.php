<?php
require_once 'Mobile/Profile/Docomo/Abstract.php';
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Docomo_Imotion extends Mobile_Profile_Docomo_Abstract
{
    public function collect()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/service/imode/make/content/spec/imotion/index.html';

            $_Device = 'Mobile_Profile_Filter_Docomo_Device';

            $profile = new Diggin_Scraper();
            $profile->process('/td[not(@scope)][1]/span', "device => RAW, $_Device")
                    ->process('/td[not(@scope)][2]/span', "filesize => TEXT")
                    ->process('/td[not(@scope)][3]/img', "telop => @alt")
                    ->process('/td[not(@scope)][4]/img', "3d => @alt");
            $section = new Diggin_Scraper();
            $section->process('div.titlept01 a', "version => TEXT")
                    ->process('//table/tr', array('profile[]' => $profile));
            $scraper = new Diggin_Scraper();
            $scraper->process('div.boxArea > div.wrap > div.section', array('section[]' => $section))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        $imotion_version = null;
        foreach ($scraper->section as $section) {
            preg_match('/MobileMP4バージョン(\d+)/', $section['version'], $match);
            $imotion_version = $match[1];

            foreach ($section['profile'] as $profile) {
                $row = array();

                $row['device']  = $profile['device']['device'];
                $row['model']   = $profile['device']['model'];
                $row['version'] = $imotion_version;

                $bytes = $profile['filesize'];
                if (preg_match('/(\d+)Mbyte$/', $bytes, $match)) {
                    $bytes = $match[1] * 1024 * 1024;
                } elseif(preg_match('/(\d+)Kbyte$/', $bytes, $match)) {
                    $bytes = $match[1] * 1024;
                }
                $row['filesize'] = (string)$bytes;

                $row['telop'] = ($profile['telop'] === '対応');
                $row['3d']    = ($profile['3d'] === '対応');

                $result[] = $row;
            }
        }

        return $result;
    }
}
