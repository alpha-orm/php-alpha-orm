<?php
namespace AlphaORM\Drivers;

class PostgreSQLDriver extends Driver
{
    const REQUIRED_FIELDS = [ 'host', 'database', 'user', 'password' ];
}
