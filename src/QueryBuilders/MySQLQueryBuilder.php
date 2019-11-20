<?php
namespace AlphaORM\QueryBuilders;

use AlphaORM\AlphaRecord;

class MySQLQueryBuilder extends QueryBuilder
{

	const DATA_TYPE = [ 'double' => 'double', 'string' => 'text', 'boolean' => 'smallint', 'integer' => 'int' ];

	static function createTable(string $table_name): string
	{
		return "CREATE TABLE IF NOT EXISTS `{$table_name}` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`id`))";
	}

	static function find(bool $single, string $tablename, string $where, array $map = []): string
	{
		$sql = "SELECT * FROM `{$tablename}` WHERE ";
		$columns = array_keys($map);
		preg_match_all('#:[a-zA-Z]+#', $where, $matches);
        $matches = $matches[0];

        if (count($map) === 0) {
            $sql .= $where;
            $sql .= $single ? ' LIMIT 1;' : ';';
            return $sql;
        }
        if (count($matches) !== count($columns)) {
            throw new Exception(constant('UNEQUAL_BOUNDED_PARAMETER'));
        }
        foreach ($matches as $match) {
        	$i = str_replace(':', '', $match);
        	if (!isset($map[$i])) {
        		throw new Exception(VARIABLE_NOT_PRESENT($i));
        	}
        	$val = $map[$i];
        	$val = is_string($val) ? str_replace("'", "\'", $val) : $val ;
        	$val = is_bool($val) ? $val === true ? 1 : 0 : $val;
        	$val = json_encode($val);
        	$where = str_replace($match, $val, $where);
        }
        $sql .= $where;
        $sql .= $single ? ' LIMIT 1;' : ';';
        return $sql;
	}

	static function deleteRecord(AlphaRecord $alpha_record): string
	{
		return "DELETE FROM `{$alpha_record->getTableName()}` WHERE `id` = {$alpha_record->getID()}";
	}

	static function updateRecord(string $tablename, AlphaRecord $map, int $id): string
	{
		$sql = "UPDATE `{$tablename}` SET";
		$columns = getProperties($map);
		foreach (getProperties($map) as $column) {
            if (in_array($column, [ '_id', 'id', '_tablename' ])) { continue; }
			$colVal = $map->{$column};
			$colVal = is_bool($colVal) ? $colVal == true ? 1 : 0 : $colVal;
			$colVal = json_encode($colVal);
			if (in_array($column, [ '_id', '_tablename', 'id' ])) { continue; }
			$sql .= "`{$column}` = {$colVal}";
			$sql .= $column == end($columns) ? " WHERE `id` = {$id}" : ', '; 
		}
		return $sql;
	}

	static function insertRecord(string $tablename, AlphaRecord $map): string
	{

		$sql = "INSERT INTO `{$tablename}` (";
		$columns = getProperties($map);
		foreach ($columns as $column) {
            if (in_array($column, [ '_tablename', '_id' ])) { continue; }
            if ($column !== 'id' and $map->{$column} instanceof AlphaRecord) { $column .= '_id'; }
           	$sql .= "`{$column}`";
            $sql .= str_replace('_id', '', $column) == end($columns) ? ') VALUES (' : ',';
        }
        foreach ($columns as $column) {
        	if (in_array($column, [ '_tablename', '_id' ])) { continue; }
        	if ($column == 'id') {
        		$colVal = $map->getID();
        	}else{
            	$colVal = $map->{$column};                
                if ($column !== 'id' and $map->{$column} instanceof AlphaRecord) { $colVal = $map->{$column}->getID(); }
        	}
            $colVal = is_bool($colVal) ? $colVal === true ? 1 : 0 : $colVal;
            $sql .= json_encode($colVal);
            $sql .= $column == end($columns) ? ');SELECT LAST_INSERT_ID();' : ',';
        }
        return $sql;
	}

	static function getAllRecords(string $tablename): string
	{
        return "SELECT * FROM `{$tablename}`";
    }

    static function updateColumns(string $tablename, array $map): string
    {
		$sql = "ALTER TABLE `{$tablename}`";
        $columns = array_keys($map);
        foreach ($columns as $column) {
        	$sql .= "MODIFY COLUMN `{$column}` {$map[$column]}";
        	$sql .= $column == end($columns) ? ';' : ',';
        }
        return $sql;
    }

    static function getColumns(string $tablename): string
    {
        return "DESCRIBE `{$tablename}`";
    }

    static function createColumns(string $tablename, array $map): string
    {
    	$sql = "ALTER TABLE `{$tablename}` ";
        $columns = array_keys($map);
        foreach ($columns as $column) {
            if (in_array($column, [ '_id', '_tablename' ])) { continue; }
            $sql .= "ADD COLUMN IF NOT EXISTS `{$column}` {$map[$column]}";
            $sql .= $column == end($columns) ? `;` : ',';
        }
        return $sql;
    }
}