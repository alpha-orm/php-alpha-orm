<?php
namespace AlphaORM\Drivers;

class MySQLDriver extends Driver
{
    const REQUIRED_FIELDS = [ 'host', 'database', 'user', 'password' ];
}
