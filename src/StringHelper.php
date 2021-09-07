<?php

namespace sagittaracc;

use Exception;

class StringHelper
{
    private static $braces = [
        '[' => ']',
        '{' => '}',
        '(' => ')',
        '<' => '>',
    ];

    public static function getOpenBraceList()
    {
        return array_keys(self::$braces);
    }

    public static function getCloseBraceList()
    {
        return array_values(self::$braces);
    }

    public static function getCloseBraceFor($brace)
    {
        if (!in_array($brace, self::getOpenBraceList())) {
            throw new Exception("$brace isn't an open brace", 400);
        }

        return self::$braces[$brace];
    }

    public static function getCloseBraceInStringFor($brace, $s, $pos)
    {
        if (mb_substr($s, $pos, 1) !== $brace) {
            throw new Exception("There is no $brace at $pos", 401);
        }

        $stack = [];

        for ($i = $pos + 1; $i < mb_strlen($s); $i++) {
            $c = mb_substr($s, $i, 1);

            if ($c === self::getCloseBraceFor($brace)) {
                if (empty($stack)) {
                    return $i;
                }
                else {
                    array_pop($stack);
                }
            }
            else if ($c === $brace) {
                array_push($stack, $c);
            }
        }

        return null;
    }

    public static function getBodyInsideBraces($brace, $s, $startPos)
    {
        $body = mb_substr($s, $startPos + 1, self::getCloseBraceInStringFor($brace, $s, $startPos) - $startPos - 1);

        return rtrim(ltrim($body, "\n\r"), " \t\n\r");
    }

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
