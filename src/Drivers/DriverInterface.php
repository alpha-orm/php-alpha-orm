<?php
namespace AlphaORM\Drivers;

interface DriverInterface {
    function connect();

    function query();

    function createTable();

    function getAll();

    function insertRecord();

    function updateRecord();

    function getColumns();

    function updateColumns();

    function createColumns();

    function find();

    function findAll();

    function store();

    function drop();

    static function getDriver($driver) {
        $driver = $driver.toLocaleLowerCase()
        switch ($driver) {
            case 'mysql':
                return MySQLDriver::class
                break;
            case 'sqlite':
                return SQLiteDriver::class
                break;
            case 'pgsql':
                return PostgreSQLDriver::class
                break;
            default:
                throw new Exception("'{$driver}' is not a supported database. Supported databases includes mysql, sqlite and pgsql")
                break;
        }
    }
}