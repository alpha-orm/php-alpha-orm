<?php
namespace AlphaORM;

use AlphaORM\QueryBuilders\QueryBuilder;

class AlphaRecord {

	private $_tablename = '';
	private $id = null;

    public function setID(int $id): void
    {
        $this->id = $id;
    }

    public function deleteID()
    {
        $this->id = null;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getTableName(): string
    {
        return $this->_tablename;
    }

	function __construct(string $tablename,int $id = null)
    {
    	$this->_tablename = $tablename;
    	if ($id) {
    		$this->id = $id;
    	}
    	return $this;
    }

    static function create(string $tablename, array $rows, bool $single = false)
    {
        $map = getTableMap($tablename);
    	$records = [];
    	foreach ($rows as $row) {
    		$record = new AlphaRecord($tablename, $row->id);
    		unset($row->id);
    		foreach (getProperties($row) as $column) {
    			if (endsWith($column, '_id')) {
    				$table = str_replace('_id', '', $column);
    				$record->{$table} = self::handleEmbedding($_ENV['DRIVER'], $table, $row[$column]);
    				continue;
    			}
    			$record->{$column} = startsWith ($map[$column], QueryBuilder::getQueryBuilder($_ENV['DRIVER'])::DATA_TYPE['boolean']) ? ($row->{$column} == 1) : $row->{$column};
    		}
    		$records[] = $record;
    	}
    	return $single ? $records[0] : $records; 
    }

    static function handleEmbedding(string $driver, string $tablename, int $id): AlphaRecord
    {
    	return AlphaORM::find($tablename, 'id = :id', [ 'id' => $id ]);
    }
}