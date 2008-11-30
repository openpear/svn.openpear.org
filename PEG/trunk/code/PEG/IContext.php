<?php

interface PEG_IContext
{
    function tell();
    function seek($i);
    function read($i);
    function lookahead($i);
    function eos();
}