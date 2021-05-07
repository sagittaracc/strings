<?php

namespace sagittaracc;

class StringHelper
{
    public static function trimHereDoc($s)
    {
        return implode("", array_map('trim', explode("\n", $s)));
    }
}
