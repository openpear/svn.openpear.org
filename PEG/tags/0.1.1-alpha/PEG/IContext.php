<?php

interface PEG_IContext
{
    function tell();
    function seek($i);
    function read($i = 1);
    function lookahead($i = 1);
    function eos();
}