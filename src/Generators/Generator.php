<?php
namespace AlphaORM\Generators;
use AlphaORM\AlphaRecord;


abstract class Generator
{
    abstract static function checkColumnUpdates(array $columns_db, array $columns_record, AlphaRecord $alpha_record);

    abstract static function creatNewColumns(array $map, AlphaRecord $alpha_record, string $tablename);

    abstract static function columns(array $columns_db, AlphaRecord $alpha_record, bool $base);

    static function getGenerator(string $driver): Generator
    {
        $driver = strtolower($driver);
        switch ($driver) {
            case 'mysql':
                return new MySQLGenerator;
                break;
        }
    }
}