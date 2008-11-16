<?php
class Sample_Webdb48_Greeting
{
    public static function greet($message = null) {
        if (is_null($message) || $message === '') {
            $message = 'World';
        }
        echo 'Hello, ' . $message . ' !' . PHP_EOL;
    }
}
