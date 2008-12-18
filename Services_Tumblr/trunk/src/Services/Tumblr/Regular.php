<?php


class Services_Tumblr_Regular extends Services_Tumblr
{
    protected $_write_default = array(
    );

    protected $_write_required = array(
        'type',
        'title',
        'body',
    );

}
