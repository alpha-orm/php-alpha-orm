<?php
namespace AlphaORM;

class AlphaRecord {
	function __construct(string $tablename, bool $fresh = true):AlphaRecord
    {
    }

    static function create(string $tablename, array $rows, bool $single = false): AlphaRecord
    {
    }

    static function handleEmbedding(string $driver, string $tablename, int $id): AlphaRecord
    {
    }
}