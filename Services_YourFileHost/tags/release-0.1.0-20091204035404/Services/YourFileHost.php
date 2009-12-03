<?php
class Services_YourFileHost {
    /**
     * ����URL
     * @var string
     */
    private $_url;

    /**
     * ����URL�̃N�G���p����
     * @var string
     */
    private $_params;

    /**
     * ��API�̃N�G���p����
     * @var string
     */
    private $_query;

    /**
     * �R���X�g���N�^
     * @param string $url
     */
    public function __construct($url = null) {
        if ($url === null) throw new Exception;
        if (!$this->_varidateUrl($url)) throw new Exception;
        $this->_url = $url;
        $this->_connect($url);
    }
    /**
     * �}�W�b�N���\�b�h
     *
     * @param string $name
     * @return ��API��value(video_id/photo) ���݂��Ȃ��ꍇ��null��Ԃ�
     */
    public function __get($name) {
        if (array_key_exists($name, $this->_query)) {
            return urldecode($this->_query[$name]);
        }
        return null;
    }
    /**
     * YourFileHost��param�v�f���X�N���C�s���O���ė�API�֐ڑ����܂�
     * 
     * @param  ����URL $url
     * @return void
     */
    private function _connect($url) {
        $html = file_get_contents($url);
        $html = preg_replace('/iso-8859-1/i', 'UTF-8', $html);
        $html = mb_convert_encoding($html, 'UTF-8', 'iso-8859-1');

        $doc = new DOMDocument;
        @$doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $tags = $xpath->query('//param[@name="movie"]');
        if ($tags->length === 0) {
            return;
        }
        $tag = $tags->item(0);
        $value = parse_url($tag->getAttribute('value'));
        $params = $this->_setParams(explode('&', $value['query']));
        $this->_query = $this->_setParams(explode('&', file_get_contents(urldecode($params['video']))));
    }
    /**
     * YourFileHost��URL�`�F�b�N
     *
     * @param  ����URL $url
     * @return boolean ������URL�̏ꍇ��true �������Ȃ��ꍇ�� false ��Ԃ��܂�
     */
    private function _varidateUrl($url) {
        $hash = parse_url($url);
        if (!$hash) return false;
        if ($hash['host'] !== 'www.yourfilehost.com' ||
                $hash['path'] !== '/media.php') {
            return false;
        }
        $this->_params = $this->_setParams(explode('&', $hash['query']));
        if (!array_key_exists('file', $this->_params)) {
            return false;
        }
        return true;
    }
    /**
     * ����URL�̃p�����[�^���p�[�X���ĕϐ��ɃZ�b�g���܂�
     *
     * @param  �N�G���p���� $params
     * @return �p�[�X����
     */
    private function _setParams($params) {
        $q = array();
        foreach ($params as $param) {
            list($key, $value) = explode('=', $param);
            $q[$key] = $value;
        }
        return $q;
    }
}
