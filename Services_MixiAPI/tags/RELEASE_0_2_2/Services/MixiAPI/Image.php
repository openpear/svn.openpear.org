<?php
final class Services_MixiAPI_Image
{
    private $filename = null;
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function getFilename()
    {
        return $this->filename;
    }
    public function getContentType()
    {
        return $this->contents['title'];
    }
    public function getContentsData()
    {
        if (is_readable($this->filename)) {
            return file_get_contents($this->filename);
        }

        throw new RuntimeException($this->filename . 'not found');
    }
}
