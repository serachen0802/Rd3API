<?php

// 資料庫連線
class Connect
{
    public $db;
    function __construct()
    {
        $dsn = 'mysql:host=localhost; dbname=rd3; charset=utf8';
        $this->db = new PDO(
            $dsn,
            'root',
            '',
            [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
}
