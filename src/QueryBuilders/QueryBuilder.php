<?php
namespace AlphaORM\QueryBuilders;

abstract class QueryBuilder {

    abstract function createTable();

    abstract function createColumns();

    abstract function getColumns();

    abstract function updateColumns();

    abstract function getAllRecords();

    abstract function insertRecord();

    abstract function updateRecord();

    abstract function deleteRecord();

    abstract function find();

    static function getQueryBuilder(string $driver): QueryBuilder
    {
        $driver = strtolower($driver);
        switch ($driver) {
            case 'mysql':
                return MySQLQueryBuilder::class;
                break;
            default:
                throw new Exception("'${driver}' is not a supported database. Supported databases includes mysql");
                break;
        }
    }
}