<?php

/**
 * @author Dinarys <support@dinarys.com>
 * @copyright 2021 Dinarys, https://dinarys.com/
 */
declare(strict_types=1);


namespace Manager;

/**
 * Class Config
 * @package Manager
 */
class Config
{

    const PATH = '../config.ini';

    const SUB_BD = 'mysql';

    /**
     * @var
     */
    private $data;

    /**
     * Config constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->initData();
    }

    /**
     * @throws \Exception
     */
    private function initData()
    {
        try {
            $this->data = parse_ini_file(self::PATH);

            if(!$this->data) throw new \Exception('Wrong path to file');
        } catch (\Exception $e) {
            throw new \Exception("Problem with file: ".$e->getMessage());
        }
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->data['db_host'];
    }

    /**
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->data['db_user'];
    }

    /**
     * @return string|null
     */
    public function getPass(): ?string
    {
        return $this->data['db_pass'];
    }

    /**
     * @return string|null
     */
    public function getDbName(): ?string
    {
        return $this->data['db_name'];
    }

    /**
     * @return string
     */
    public function getSubBd(): string
    {
        return self::SUB_BD;
    }

}
