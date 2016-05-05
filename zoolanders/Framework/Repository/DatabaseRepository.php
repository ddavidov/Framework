<?php

namespace Zoolanders\Repository;

use Zoolanders\Service\Database;

abstract class DatabaseRepository extends Repository
{
    /**
     * @var Database
     */
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }
}