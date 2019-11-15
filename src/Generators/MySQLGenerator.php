<?php
namespace AlphaORM\Generators;

use AlphaORM\QueryBuilders\MySQLQueryBuilder;
use AlphaORM\AlphaRecord;
use AlphaORM\AlphaORM;

class MySQLGenerator extends Generator
{

    static function checkColumnUpdates(array $columns_db, array $columns_record, AlphaRecord $alpha_record): array
    {
    }

    static function creatNewColumns(array $map, AlphaRecord $alpha_record, string $tablename): array
    {
    }

    static function async columns(array $columns_db, AlphaRecord $alpha_record, bool $base = true): array
    {
    }
}