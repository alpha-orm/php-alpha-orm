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
	return array_map(function ($element) {
		return $element->name;
	}, ((new ReflectionObject($object))->getProperties()));
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

# Error Messages
function DRIVER_NOT_SUPPORTED(string $driver): string
{ return "'{$driver}' is not a supported database. Supported databases includes mysql"; }

function VARIABLE_NOT_PRESENT(string $var): string
{ return "Variable '{$var}' is not present in parameters"; }