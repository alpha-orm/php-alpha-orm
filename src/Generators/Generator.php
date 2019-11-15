<?php
namespace AlphaORM\Generators;

abstract class Generator
{

    abstract function checkColumnUpdates();

    abstract function creatNewColumns();

    abstract function columns();

    static function getGenerator(string $driver): Generator
    {
        $driver = strtolower($driver);
        switch ($driver) {
            case 'mysql':
                return MySQLGenerator::class;
                break;
            default:
                throw new Exceptiion("'${driver}' is not a supported database. Supported databases includes mysql, sqlite and pgsql");
                break;
        }
    }
}