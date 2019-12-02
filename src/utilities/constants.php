<?php
define("RECORD_NOT_FOUND", "Record matching query not found");
define('SETUP_ERROR', 'Connection to database has not been set yet!');
define('UNEQUAL_BOUNDED_PARAMETER', 'Number of bounded parameters is not equal to variables');
define('DB_VARIABLE_ERROR', 'Values of can only be number, string or boolean');
define('RECORD_NOT_STORED', 'This record is not stored yet');

# Error Messages
function DRIVER_NOT_SUPPORTED(string $driver): string
{ return "'{$driver}' is not a supported database. Supported databases includes mysql"; }

function SETUP_OPTION_MISSING(string $option): string
{ return "The '{$option}' option is required for this database!"; }

function VARIABLE_NOT_PRESENT(string $var): string
{ return "Variable '{$var}' is not present in parameters"; }