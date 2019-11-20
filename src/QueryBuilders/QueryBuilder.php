<?php
namespace AlphaORM\QueryBuilders;

use AlphaORM\AlphaRecord;

abstract class QueryBuilder {

    abstract static function createTable(string $table_name);

    abstract static function createColumns(string $tablename, array $map);

    abstract static function getColumns(string $tablename);

    abstract static function updateColumns(string $tablename, array $map);

    abstract static function getAllRecords(string $tablename);

    abstract static function insertRecord(string $tablename, AlphaRecord $map);

    abstract static function updateRecord(string $tablename, AlphaRecord $map, int $id);

    abstract static function deleteRecord(AlphaRecord $alpha_record);

    abstract static function find(bool $single, string $tablename, string $where, array $map = []);

    static function getQueryBuilder(string $driver): QueryBuilder
    {
        $driver = strtolower($driver);
        switch ($driver) {
            case 'mysql':
                return new MySQLQueryBuilder;
                break;
        }
    }
}