<?php
namespace AlphaORM\QueryBuilders;
use AlphaORM\AlphaRecord;

class PostgreSQLQueryBuilder extends QueryBuilder
{

    const DATA_TYPE =  [ 'double' => 'double', 'string' => 'text', 'boolean' => 'smallint', 'integer' => 'integer' ];

    static function getSchema(): string
    { return $_ENV['OPTION']['schema'] ?? 'public'; }


    static function createTable($tablename): string
    {
        return "CREATE TABLE IF NOT EXISTS ".self::getSchema().".\"{$tablename}\" ( id SERIAL, PRIMARY KEY (id) );";
    }

    static function createColumns(string $tablename, array $map): string
    {
        $sql = "ALTER TABLE ".self::getSchema().".\"{$tablename}\" ";
        $columns = array_keys($map);
        foreach ($columns as $column) {
            if ($column == '_id' or $column == '_tablename') { continue; }
            $sql .= "ADD COLUMN IF NOT EXISTS {$column} {$map[$column]}";
            $sql .= $column == end($columns) ? `;` : ',';
        }
        return $sql;
    }

    static function getColumns(string $tablename): string
    {
        return "SELECT column_name AS \"Field\", data_type as \"Type\" FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='{$tablename}'";
    }

    static function updateColumns(string $tablename, array $map): string
    {
        $sql = "ALTER TABLE ".self::getSchema().".\"${tablename}\"";
        $columns = array_keys($map);
        foreach ($columns as $column) {
            $sql .= "MODIFY COLUMN {$column} {$map[$column]}";
            $sql .= $column == end($columns) ? `;` : ',';
        }
        return $sql;
    }

    static function getAllRecords(string $tablename): string
    {
        return "SELECT * FROM ".self::getSchema().".\"{$tablename}\"";
    }

    static function insertRecord(string $tablename, AlphaRecord $map): string
    {
        $sql = "INSERT INTO ".self::getSchema().".\"${tablename}\" (";
        $columns = getProperties($map);
        foreach ($columns as $column) {
            if ($column == '_tablename' or $column == 'id') { continue; }
            $sql .= $column;
            $sql .= $column == end($columns) ? ') VALUES (' : ',';
        }
        foreach ($columns as $column) {
            if ($column == '_tablename' | $column == 'id') { continue; }
            $colVal = $map->{$column};
            if (is_bool($colVal)) {
                $colVal = true == $colVal ? 1 : 0;
            }
            $sql .= str_replace('"', "'", json_encode($colVal));
            $sql .= $column == end($columns) ? ");" : ',';
        }
        return $sql;
    }

    static function updateRecord(string $tablename, AlphaRecord $map, int $id): string
    {
        $sql = "UPDATE ".self::getSchema().".\"{$tablename}\" ";
        $columns = getProperties($map);
        foreach ($columns as $column) {
            $colVal = $map[$column];
            if (is_bool($colVal)) {
                $colVal = true == $colVal ? 1 : 0;
            }
            $colVal = str_replace('"', "'", json_encode($colVal));
            $sql .= $column == $columns[0] ? 'SET ' : '';
            if ($column == '_id' or $column == '_tablename' or $column == 'id') { continue; }
            $sql .= "{$column} = {$colVal}";
            $sql .= $column == end($columns) ? " WHERE id = {$id};" : ', ';
        }
        return $sql;
    }

    static function deleteRecord(AlphaRecord $map): string
    {
        return "DELETE FROM {$map->_tablename} WHERE id = {$map->_id}";
    }

    static function find(bool $single, string $tablename, string $where, array $map = []): string
    {
        $sql = "SELECT * FROM ".self::getSchema().".\"{$tablename}\" WHERE ";
        $columns = array_keys($map);
        preg_match_all('#:[a-zA-Z]+#', $where, $matches);
        if (empty($map)) {
            $sql .= $where;
            $sql .= $single ? ' LIMIT 1;' : ';';
            return $sql;
        }
        if (count($matches) !== count($columns)) {
            throw new \Exception(constant('UNEQUAL_BOUNDED_PARAMETER'));
        }
        foreach ($matches as $match) {
        	$i = str_replace(':', '', $match);
        	if (!isset($map[$i])) {
        		throw new \Exception(VARIABLE_NOT_PRESENT($i));
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

    static function dropAll(string $tablename):string
    {
        return "DROP FROM `{$tablename}`";
    }
}