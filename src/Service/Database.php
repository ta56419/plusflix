<?php

namespace App\Service;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    protected static function init(): void
    {
        self::$pdo = new PDO(
            Config::get('db_dsn'),
            Config::get('db_user'),
            Config::get('db_pass')
        );

        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getPDO(): PDO
    {
        if (! self::$pdo) {
            self::init();
        }

        return self::$pdo;
    }
}
