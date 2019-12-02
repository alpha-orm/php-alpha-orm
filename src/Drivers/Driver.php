<?php
namespace AlphaORM\Drivers;

use AlphaORM\AlphaORM;
use AlphaORM\AlphaRecord;
use AlphaORM\QueryBuilders\QueryBuilder;
use AlphaORM\Generators\Generator;

abstract class Driver
{
    static function connect(): bool
    {
        if (!isset($_ENV['DRIVER'])) { throw new \Exception(constant('SETUP_ERROR')); }
        if ($_ENV['DRIVER'] == 'sqlite') {
            die('sqlite not supported!');
        }else{
            $_ENV['CONNECTION'] = new \PDO("{$_ENV['DRIVER']}:host={$_ENV['OPTIONS']['host']};dbname={$_ENV['OPTIONS']['database']}", $_ENV['OPTIONS']['user'], $_ENV['OPTIONS']['password']);
            return($_ENV['CONNECTION'] instanceof  PDO);
        }
    }

    static function query(string $sql)
    {
        self::connect();
        $stmnt = $_ENV['CONNECTION']->query($sql);
        $retVal['response'] = $stmnt->fetchAll(\PDO::FETCH_OBJ);
        if ($_ENV['CONNECTION']->lastInsertId() > 0) {
            $retVal['connection'] = $_ENV['CONNECTION']->lastInsertId();
        }
        return $retVal;
    }

    static function createTable(string $tablename)
    {
        self::connect();
        extract(self::query(QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::createTable($tablename)));
        return $response;
    }

    static function getAll(string $tablename): array
    {        
        self::connect();
        extract(self::query(QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::getAllRecords($tablename)));
        return AlphaRecord::create($tablename, array_values($response));
    }

    static function insertRecord(string $tablename, AlphaRecord $alpha_record)
    {                
        self::connect();
        extract(self::query(QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::insertRecord($tablename, $alpha_record)));
        return $connection;
    }

    static function updateRecord(string $tablename, AlphaRecord $alpha_record)
    {                
        foreach (getProperties($alpha_record) as $col) { 
            if (in_array($col, [ '_id', 'id', '_tablename' ])) { continue; }        
            if ($alpha_record->{$col} instanceof AlphaRecord) {
                $col .= '_id';
                $alpha_record->{$col} = self::updateRecord($alpha_record->{$col});
                // unset($alpha_record->{$col});
            }
        }
        self::connect();
        extract(self::query(QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::updateRecord($tablename, $alpha_record, $alpha_record->getID())));
        return $response;
    }

    static function getColumns(string $tablename)
    {
        self::connect();
        extract(self::query(QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::getColumns($tablename)));
        return $response;
    }

    static function updateColumns(string $tablename, array $updated_columns)
    {
        self::connect();
        extract(self::query(QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::updateColumns($tablename, $updated_columns)));
        return $response;
    }

    static function createColumns(string $tablename, array $new_columns)
    {
        self::connect();
        extract(self::query(QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::createColumns($tablename, $new_columns)));
        return $response;
    }

    static function find(string $tablename, string $where, array $map = [])
    {
        self::connect();
        extract(self::query(QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::find(true, $tablename, $where, $map)));
        if (count($response) !== 1) {
            throw new \Exception(constant('RECORD_NOT_FOUND'));        
        }
        return AlphaRecord::create($tablename, array_values($response), true);
    }

    static function findAll(string $tablename, string $where, array $map = []): array
    {
        self::connect();
        extract(self::query(QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::find(false, $tablename, $where, $map)));
        return AlphaRecord::create($tablename, array_values($response));
    }

    static function store(AlphaRecord $alpha_record)
    {

        foreach (getProperties($alpha_record) as $a) {
            if (in_array($a, [ '_tablename', 'id', '_id' ])) { continue; }
            if ($alpha_record->{$a} instanceof AlphaRecord) {
                $alpha_record->{$a} = self::store($alpha_record->{$a});
            }
        }

        $columns_db = self::getColumns($alpha_record->getTableName());
        
        extract(Generator::getGenerator($_ENV['DRIVER'])::columns($columns_db, $alpha_record));

        if (!empty($updated_columns)) { self::updateColumns($alpha_record->getTableName(), $updated_columns); }

        if (!empty($new_columns)) { self::createColumns($alpha_record->getTableName(), $new_columns); }

        if ($alpha_record->getID()) {
            foreach (getProperties($alpha_record) as $col) {
            if (in_array($col, [ '_tablename', 'id', '_id' ])) { continue; }
                if ($alpha_record->{$col} instanceof AlphaRecord) {
                    $alpha_record->{$col} = self::store($alpha_record->{$col});
                }
            }
            return self::updateRecord($alpha_record->getTableName(), $alpha_record);
        }
        $alpha_record->setID(self::insertRecord($alpha_record->getTableName(), $alpha_record));
        return $alpha_record;
    }

    static function drop(AlphaRecord $alpha_record)
    {
        if (!$alpha_record->getID()) { throw new \Exception(constant('RECORD_NOT_STORED')); }
       self::query(QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::deleteRecord($alpha_record));
       $alpha_record->deleteID(); 
    }

    static function dropAll(string $tablename)
    {
       self::query(QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::deleteAllRecords($tablename));
    }

    static function getDriver(string $driver): Driver
    {
        $driver = strtolower($driver);
        switch ($driver) {
            case 'mysql':
                return new MySQLDriver;
                break;
            case 'pgsql':
                return new PostgreSQLDriver;
                break;
        }
    }
}