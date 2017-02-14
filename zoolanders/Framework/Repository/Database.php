<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Repository;

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