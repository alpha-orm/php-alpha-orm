<?php
namespace AlphaORM\Drivers;

abstract class Driver
{
    abstract function connect();

    abstract function query();

    abstract function createTable();

    abstract function getAll();

    abstract function insertRecord();

    abstract function updateRecord();

    abstract function getColumns();

    abstract function updateColumns();

    abstract function createColumns();

    abstract function find();

    abstract function findAll();

    abstract function store();

    abstract function drop();

    static function getDriver(string $driver): Driver
    {
        $driver = strtolower($driver);
        if (!in_array($driver, AlphaORM::SUPPORTED_DATABASES)) { throw new Exception("'{$driver}' is not a supported database. Supported databases includes mysql"); }
        switch ($driver) {
            case 'mysql':
                return MySQLDriver::class;
                break;
        }
    }
}