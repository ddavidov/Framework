<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Repository;

interface RepositoryInterface
{
    public function create(array $data);

    public function all();

    public function get($id);

    public function delete($ids);
}