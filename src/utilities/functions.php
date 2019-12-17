<?php
use AlphaORM\Drivers\Driver;

function contains(string $needle, string $haystack): bool
{
	return (strpos($haystack, $needle) !== false);
}

function startsWith (string $string, string $startString): bool
{ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
} 

function endsWith(string $string, string $endString): bool
{ 
    $len = strlen($endString); 
    if ($len == 0) { 
        return true; 
    } 
    return (substr($string, -$len) === $endString); 
}

function getProperties(Object $object)
{
	return array_values(array_filter(array_map(function ($element) {
        return in_array($element->name, ['_tablename','id']) ? '' : $element->name;
    }, ((new ReflectionObject($object))->getProperties()))));
}

function getTableMap(string $tablename)
{
    $g = array_map(function($element){
        return [ $element->Field => $element->Type ];
    }, Driver::getColumns($tablename));
    $f = [];
    foreach ($g as $key => $value) {
        foreach ($value as $key1 => $value1) {
            $f[$key1] = $value1;
        }
    }
    return $f;
}
