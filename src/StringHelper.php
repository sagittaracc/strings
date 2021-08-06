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
    /**
     * TODO: Выпилить (неверно названа)
     */
    public static function caseDivided($s)
    {
        return implode('', array_map(
            function ($part) {
                return ucfirst($part);
            },
            explode('_', $s)
        ));
    }

    public static function camel2id($name)
    {
        $regex = '/(?<!\p{Lu})\p{Lu}/u';
        return strtolower(trim(preg_replace($regex, '_\0', $name), '_'));
    }

    public static function id2camel($id)
    {
        return implode('', array_map(
            function ($part) {
                return ucfirst($part);
            },
            explode('_', $id)
        ));
    }
}
