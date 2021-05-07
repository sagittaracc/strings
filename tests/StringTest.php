<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class StringTest extends TestCase
{
    public function testStringStripDoubleSpaces(): void
    {
        $hereDocString = <<<STR
            I'm sagittaracc.
                This is just some tests i'm doing here.
            If it's not working there's nothing i can do about it, cuz it really should.
                Okay?
STR;
        $this->assertEquals(
            "I'm sagittaracc.".
            "This is just some tests i'm doing here.".
            "If it's not working there's nothing i can do about it, ".
            "cuz it really should.Okay?",
            StringHelper::trimHereDoc($hereDocString)
        );
    }
}
