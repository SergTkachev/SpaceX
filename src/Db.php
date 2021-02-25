<?php

/**
 * @author Dinarys <support@dinarys.com>
 * @copyright 2021 Dinarys, https://dinarys.com/
 */
declare(strict_types=1);


namespace Manager;

use PDO;

/**
 * Class Db
 * @package Manager
 */
class Db
{

    /**
     * @var string|null
     */
    private $dbHost;

    /**
     * @var string|null
     */
    private $dbUser;

    /**
     * @var string|null
     */
    private $dbPassword;

    /**
     * @var string|null
     */
    private $dbName;

    /**
     * @var string
     */
    private $db;

    /**
     * @var \Manager\Config
     */
    private $config;

    /**
     * @var
     */
    private static $instance;

    /**
     * @var PDO
     */
    private PDO $connection;

    /**
     * @return mixed
     */
    public static function get_instance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Db constructor.
     */
    protected function __construct()
    {
        $this->config = new Config();

        $this->dbUser = $this->config->getUser();
        $this->dbPassword = $this->config->getPass();
        $this->dbName = $this->config->getDbName();
        $this->dbHost = $this->config->getHost();
        $this->db = $this->config->getSubBd();
    }


    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        $dsn = $this->db . ':host=' . $this->dbHost . ';dbname=' . $this->dbName;

        try {
            $this->connection = new PDO($dsn, $this->dbUser,$this->dbPassword);
        } catch (\PDOException $ex) {
            throw new \PDOException('MySQL connection error: ' . $ex->getMessage());
        }


        return $this->connection;
    }

}
