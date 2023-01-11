<?php

namespace Hexlet\Code;

class Connection
{
    /**
     * Connection
     * @var
     */
    private static $conn;


    /**
     * @return \PDO
     * @throws \Exception
     */
    public function connect()
    {
        if (array_key_exists('DATABASE_URL', $_ENV)) {
            $dbUrl = parse_url($_ENV['DATABASE_URL']);
            $params = [
                'host' => $dbUrl['host'],
                'port' => $dbUrl['port'],
                'database' => ltrim($dbUrl['path'], '/'),
                'user' => $dbUrl['user'],
                'password' => $dbUrl['pass']
            ];
        } else {
            $params = parse_ini_file('database.ini');
        }

        if ($params === false) {
            throw new \Exception('Error reading database configuration');
        }

        $conStr = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s',
            $params['host'],
            $params['port'],
            $params['database'],
            $params['user'],
            $params['password']
        );

        $pdo = new \PDO($conStr);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    public static function get()
    {
        if (null === static::$conn) {
            static::$conn = new static();
        }

        return static::$conn;
    }

    protected function __construct()
    {
    }
}
