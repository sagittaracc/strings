<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class SqlStringTest extends TestCase
{
    public function testBraces(): void
    {
        $this->assertEquals(['[', '{', '(', '<'], StringHelper::getOpenBraceList());
        $this->assertEquals([']', '}', ')', '>'], StringHelper::getCloseBraceList());

        $this->assertEquals(']', StringHelper::getCloseBraceFor('['));
        $this->assertEquals('}', StringHelper::getCloseBraceFor('{'));
        $this->assertEquals(')', StringHelper::getCloseBraceFor('('));
        $this->assertEquals('>', StringHelper::getCloseBraceFor('<'));

        $this->expectExceptionCode(400);
        StringHelper::getCloseBraceFor('!');
    }

    public function testBodyInsideBraces(): void
    {
        $s = <<<STRING
            (
                какой - то текст здесь
                (
                    some text here()()
                    ()(
                        text text
                    )
                )
            )
STRING;

        $body = <<<STRING
                какой - то текст здесь
                (
                    some text here()()
                    ()(
                        text text
                    )
                )
STRING;

        $body1 = <<<STRING
                    some text here()()
                    ()(
                        text text
                    )
STRING;

        $body2 = '';

        $this->assertEquals($body,  StringHelper::getBodyInsideBraces('(', $s, 12));
        $this->assertEquals($body1, StringHelper::getBodyInsideBraces('(', $s, 71));
        $this->assertEquals($body2, StringHelper::getBodyInsideBraces('(', $s, 108));
    }

    public function testStripSpacesAfterBraces(): void
    {
        $this->assertEquals('(', StringHelper::trimSpacesAfterBraces('(  '));
    }

    public function testStripSpacesBeforeBraces(): void
    {
        $this->assertEquals(')', StringHelper::trimSpacesBeforeBraces('  )'));
    }

    public function testStripUnionSql(): void
    {
        $actualSql = <<<SQL
            (select min(users.registration) as reg_interval from users)
                union
            (select max(users.registration) as reg_interval from users)
SQL;

        $this->assertEquals(
            '(select min(users.registration) as reg_interval from users) ' .
                'union ' .
            '(select max(users.registration) as reg_interval from users)',
            StringHelper::trimSql($actualSql)
        );
    }

    public function testStripSubQuerySql(): void
    {
        $actualSql = <<<SQL
            select
                allGroupCount.gname,
                allGroupCount.count
            from (
                select
                    groups.name as gname,
                    count(groups.name) as count
                from users
                inner join user_group on users.id = user_group.user_id
                inner join groups on user_group.group_id = groups.id
                group by groups.name
            ) allGroupCount
            where gname = 'admin'
SQL;

        $this->assertEquals(
            'select ' .
                'allGroupCount.gname, ' .
                'allGroupCount.count ' .
            'from (' .
                'select ' .
                    'groups.name as gname, ' .
                    'count(groups.name) as count ' .
                'from users ' .
                'inner join user_group on users.id = user_group.user_id ' .
                'inner join groups on user_group.group_id = groups.id ' .
                'group by groups.name' .
            ') allGroupCount ' .
            "where gname = 'admin'",
            StringHelper::trimSql($actualSql)
        );
    }

    public function testStripExpressionSql(): void
    {
        $actualSql = <<<SQL
            (
                id > ?
                    or
                id between ? and ?
            )
            and id in (?,?,?)
            and name like ?
SQL;

        $this->assertEquals(
            '(id > ? or id between ? and ?) and id in (?,?,?) and name like ?',
            StringHelper::trimSql($actualSql)
        );
    }

    public function testCaseDivided(): void
    {
        $this->assertEquals('OneTwoThree', StringHelper::caseDivided('one_two_three'));
    }

    public function testInflector(): void
    {
        $this->assertEquals('one_two_three', StringHelper::camel2id('OneTwoThree'));
        $this->assertEquals('OneTwoThree', StringHelper::id2camel('one_two_three'));
    }
}
