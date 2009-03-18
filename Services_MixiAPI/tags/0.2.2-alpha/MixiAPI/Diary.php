<?php
final class Services_MixiAPI_Diary
{
    private $image = null;
    private $contents = array();
    public function __construct($title, $contents)
    {
        $this->contents['title'] = $title;
        $this->contents['contents'] = $contents;
    }

    public function setImage(Services_MixiAPI_Image $image)
    {
        $this->image = $image;
    }
    public function getImage()
    {
        return $this->image;
    }
    public function hasImage()
    {
        return !is_null($this->image);
    }
    public function removeImage()
    {
        $this->image = null;
    }
    public function getTitle()
    {
        return $this->contents['title'];
    }
    public function getContents()
    {
        return $this->contents['contents'];
    }
    public function getContentsData()
    {
        return sprintf('<?xml version="1.0" encoding="UTF-8"?>'
                     . '<entry xmlns="http://www.w3.org/2007/app">'
                     . '<title><![CDATA[%s]]></title>'
                     . '<summary><![CDATA[%s]]></summary>'
                     . '</entry>',
                       str_replace(']]>', ']]&gt;', $this->getTitle()),
                       str_replace(']]>', ']]&gt;', $this->getContents()));
    }
}
