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
        if (!in_array($driver, AlphaORM::SUPPORTED_DATABASES)) { throw new Exception("'{$driver}' is not a supported database. Supported databases includes mysql"); }
        switch ($driver) {
            case 'mysql':
                return MySQLGenerator::class;
                break;
        }
    }
}