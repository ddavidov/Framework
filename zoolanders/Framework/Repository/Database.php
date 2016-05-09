<?php

namespace Zoolanders\Repository;

abstract class Database extends Repository
{
    /**
     * @var Database
     */
    protected $db;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        $this->db = $this->container->db;
    }
}