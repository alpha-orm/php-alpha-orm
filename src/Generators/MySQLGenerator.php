<?php
namespace AlphaORM\Generators;

use AlphaORM\QueryBuilders\MySQLQueryBuilder;
use AlphaORM\AlphaRecord;
use AlphaORM\AlphaORM;

class MySQLGenerator extends Generator
{
    static function checkColumnUpdates(array $columns_db, array $columns_record, AlphaRecord $alpha_record): array
    {
    	$updated_columns = [];
        $existing = [];
        foreach ($columns_db as $col) {
        	if (in_array($col->Field, $columns_record)) {
        		if ($col->Field !== 'id' and !(startsWith($col->Type, MySQLQueryBuilder::DATA_TYPE[gettype($alpha_record->{$col->Field})]))) {
        			if (!in_array(gettype($col->Field), AlphaORM::DATA_TYPES)) {
        				throw new \Exception(constant('DB_VARIABLE_ERROR'));        				
        			} elseif(startsWith($col->Type, MySQLQueryBuilder::DATA_TYPE['integer']) and !is_bool($alpha_record->{$col->Field})) {
        					$updated_columns[$col->Field] = MySQLQueryBuilder::DATA_TYPE[gettype($alpha_record->{$col->Field})];
        			} elseif(startsWith($col->Type, MySQLQueryBuilder::DATA_TYPE['boolean']) and !is_bool($alpha_record->{$col->Field})){
        				if (!in_array(gettype($alpha_record->{$col->Field}), [ 'integer', 'double' ])) {
        					$updated_columns[$col->Field] = MySQLQueryBuilder::DATA_TYPE[gettype($alpha_record->{$col->Field})];
        				}else{
        					$updated_columns[$col->Field] = MySQLQueryBuilder::DATA_TYPE['string'];
        				}
        			}        			
        		}
        	}
        }
        return compact('updated_columns','existing');
    }

    static function creatNewColumns(array $map, AlphaRecord $alpha_record, string $tablename): array
    {
    	$new_columns = [];
    	foreach ($map as $col) {
            if (in_array($col, [ '_tablename', 'id', '_id' ])) {
                continue;
            }
            if ($col instanceof AlphaRecord) {
                self::columns($col->getTableName(), $col);
            } else if (!in_array(gettype($col), AlphaORM::DATA_TYPES)) {
                throw new \Exception(constant('DB_VARIABLE_ERROR'));
            } else {
                if ($alpha_record->{$col} instanceof AlphaRecord) {
                    $new_columns[$alpha_record->{$col}->getTableName().'_id'] = MySQLQueryBuilder::DATA_TYPE['integer'];
                } else {
                    $new_columns[$col] = MySQLQueryBuilder::DATA_TYPE[gettype($alpha_record->{$col})];
                }
            }
        }
        return $new_columns;
    }

    static function columns(array $columns_db, AlphaRecord $alpha_record, bool $base = true): array
    {    	
        $tablename = $alpha_record->getTableName();

        unset($alpha_record->__tablename);

        $columns_record = getProperties($alpha_record);

        extract(self::checkColumnUpdates($columns_db, $columns_record, $alpha_record));
        $diff_array = array_diff($columns_record, $existing);
        $new_columns = self::creatNewColumns($diff_array, $alpha_record, $tablename);
        return compact('updated_columns', 'new_columns');
    }
}