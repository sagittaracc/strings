<?php

namespace sagittaracc;

class StringHelper
{
    public static function trimHereDoc($s)
    {
        return implode(" ", array_map('trim', explode("\n", $s)));
    }

    public static function trimSpacesAfterBraces($s)
    {
        return preg_replace('/\(\s+/', '(', $s);
    }

    public static function trimSpacesBeforeBraces($s)
    {
        return preg_replace('/\s+\)/', ')', $s);
    }

    public static function trimSql($sql)
    {
        $sql = self::trimHereDoc($sql);
        $sql = self::trimSpacesAfterBraces($sql);
        return self::trimSpacesBeforeBraces($sql);
    }
}
