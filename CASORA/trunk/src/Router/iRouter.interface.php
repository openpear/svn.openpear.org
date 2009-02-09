<?php

interface iRouter
{
    public $action;

    public $path;

    public $requests;

    public $application;

    public function __construct();

    public function build( $template );
}
