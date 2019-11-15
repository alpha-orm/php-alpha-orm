<?php
namespace AlphaORM;

use AlphaORM\Drivers\DriverInterface;

class AlphaORM
{

	const SUPPORTED_DATABASES = [ 'mysql' ];

	static function setup(string $driver, Array $options): bool
	{
    }

    static function store(AlphaRecord $alpha_record): bool
    {
    }

    static function drop(AlphaRecord $alpha_record): bool
    {
        
    }

    static function create($table_name)
    {
    }

    static function getAll($table_name)
    {
    }

    static function find(string $table_name, string $where, array $map): AlphaRecord
    {
    }

    static function findAll(string $table_name, string $where, array $map): array
    {
    }

    static function query(string $sql)
    {
    }
}