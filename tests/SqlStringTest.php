<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class SqlStringTest extends TestCase
{
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
}