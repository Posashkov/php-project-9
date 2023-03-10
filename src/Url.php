<?php

namespace Hexlet\Code;

use Hexlet\Code\Connection;
use Hexlet\Code\PostgreSQLExecutor;
use Carbon\Carbon;

class Url
{
    private string $name = '';
    private ?int $id;
    private string $created_at = '';
    private static string $tableName = 'urls';

    public function __construct()
    {
        $this->id = null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return void
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCretedAt()
    {
        return Carbon::parse($this->created_at);
    }

    /**
     * @return void
     */
    private function setField(string $name, string $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    /**
     * @return Url
     * @throws \Exception
     */
    public function store()
    {
        if ($this->getName() == '') {
            throw new \Exception('Can\'t store new url because have no url name');
        }

        $pdo = Connection::get()->connect();
        $executor = new PostgreSQLExecutor($pdo);

        if (is_null($this->getId())) {
            $sql = 'INSERT INTO ' . self::$tableName . ' (name) VALUES (:name)';
            $sqlParams = [
                ':name' => $this->getName()
            ];

            $lastId = (int)$executor->insert($sql, $sqlParams, self::$tableName);

            if ($lastId <= 0) {
                throw new \Exception('Something goes wrong. Can\'t store new url');
            }
            $this->setId($lastId);
        } else {
            // TODO: Update
        }

        return $this;
    }

    /**
     * @return Url
     * @throws \Exception
     */
    public static function byName(string $name = '')
    {
        if (trim($name) == '') {
            throw new \Exception('Can\'t select url because have no url name');
        }

        $pdo = Connection::get()->connect();
        $executor = new PostgreSQLExecutor($pdo);

        $sql = 'SELECT * FROM ' . self::$tableName . ' WHERE name=:name LIMIT 1';
        $sqlParams = [
            ':name' => $name
        ];

        $return = $executor->select($sql, $sqlParams, self::$tableName);

        return (!$return) ? self::create([]) : self::create(reset($return));
    }

    /**
     * @return Url
     * @throws \Exception
     */
    public static function byId(int $id = 0)
    {
        if ($id <= 0) {
            throw new \Exception('Can\'t select url because id = 0');
        }

        $pdo = Connection::get()->connect();
        $executor = new PostgreSQLExecutor($pdo);

        $sql = 'SELECT * FROM ' . self::$tableName . ' WHERE id=:id';
        $sqlParams = [
            ':id' => $id
        ];

        $return = $executor->select($sql, $sqlParams, self::$tableName);

        return (!$return) ? self::create([]) : self::create(reset($return));
    }

    /**
     * @param array<string, string> $fields
     * @return Url
     */
    private static function create($fields)
    {
        $url = new self();

        if (count($fields) <= 0) {
            return $url;
        }

        foreach ($fields as $key => $value) {
            $url->setField($key, $value);
        }

        return $url;
    }
}
