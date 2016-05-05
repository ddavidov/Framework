<?php

namespace Zoolanders\Repository;
use Zoolanders\Service\Zoo;

/**
 * Class TableRepository
 * Base table repository for any repository that needs to use the old ZOO table classes
 * @package Zoolanders\Repository
 */
abstract class TableRepository extends Repository
{
    /**
     * @var Zoo
     */
    protected $app;

    /**
     * @var \AppTable
     */
    protected $table;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * TableRepository constructor.
     * @param Zoo $app
     */
    public function __construct(Zoo $app)
    {
        $this->app = $app;
        $this->table = $this->app->table->$name;
    }

    public function create(array $attributes)
    {
        $object = $this->app->object->create($this->className);

        foreach ($attributes as $k => $v) {
            $object->$k = $v;
        }

        $this->table->save($object);

        return $object;
    }

    public function all()
    {
        return $this->table->all();
    }

    public function get($id)
    {
        return $this->table->get($id);
    }

    public function delete($ids)
    {
        settype($ids, 'array');

        foreach ($ids as $id) {
            $object = $this->get($id);
            $this->table->delete($object);
        }
    }
}