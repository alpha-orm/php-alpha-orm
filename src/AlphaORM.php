<?php
namespace AlphaORM;

use AlphaORM\Drivers\Driver;

class AlphaORM
{

	const SUPPORTED_DATABASES = [ 'mysql', 'pgsql' ];
    const DATA_TYPES = [ 'double', 'string' , 'boolean' , 'int'];

	static function setup(string $driver, Array $options): bool
	{
        if (!in_array($driver, self::SUPPORTED_DATABASES)) { throw new \Exception(DRIVER_NOT_SUPPORTED($driver)); }        
        $_ENV['DRIVER'] = $driver;
        $_ENV['OPTIONS'] = $options;
        foreach (Driver::getDriver($_ENV['DRIVER'])::REQUIRED_FIELDS as $option) {
            if (!array_key_exists($option,$options)) {
                throw new \Exception(SETUP_OPTION_MISSING($option));
            }
        }
        $connected = Driver::connect();
        $_ENV['CONNECTION'] = null;
        return $connected;
    }

    static function store(AlphaRecord $alpha_record): bool
    {
        Driver::getDriver($_ENV['DRIVER'])::store($alpha_record);
        return $alpha_record->getID() == null;
    }

    static function drop(AlphaRecord $alpha_record): bool
    {        
        Driver::getDriver($_ENV['DRIVER'])::drop($alpha_record);
        return $alpha_record->getID() == null;
    }

    static function dropAll(string $tablename)
    {        
        Driver::getDriver($_ENV['DRIVER'])::dropAll($tablename);
    }

    static function create(string $table_name): AlphaRecord
    {
        Driver::getDriver($_ENV['DRIVER'])::createTable($table_name);
        return new AlphaRecord($table_name);
    }

    static function getAll(string $table_name): array
    {
        return Driver::getDriver($_ENV['DRIVER'])::getAll($table_name);
    }

    static function find(string $table_name, string $where, array $map = []): AlphaRecord
    {
        return Driver::getDriver($_ENV['DRIVER'])::find($table_name, $where, $map);
    }

    static function findAll(string $table_name, string $where, array $map = []): array
    {
        return Driver::getDriver($_ENV['DRIVER'])::findAll($table_name, $where, $map);
    }

    static function query(string $sql)
    {
        return Driver::getDriver($_ENV['DRIVER'])::query($sql);
    }
}