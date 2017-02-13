<?php

namespace Zoolanders\Framework\Item;

use Zoolanders\Framework\Container\Container;

class Indexer
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var \Zoolanders\Framework\Element\Indexer
     */
    protected $elementIndexer;

    /**
     * Indexer constructor.
     * @param Container $container
     */
    public function __construct(Container $container, \Zoolanders\Framework\Element\Indexer $elementIndexer )
    {
        $this->container = $container;
        $this->elementIndexer = $elementIndexer;
    }

    /**
     * @param \Item $item
     */
    public function index(\Item $item)
    {
        $this->cleanSearchIndex($item);

        $dataToSave = [];

        /** @var \Element $element * */
        foreach ($item->getElements() as $element) {
            $dataType = $this->elementIndexer->getDataTypeFromElement($element);
            $values = $this->elementIndexer->getValuesFromElement($element);

            $dataToSave[$dataType][$element->identifier] = $values;
        }

        $db = $this->container->db;

        foreach ($dataToSave as $dataType => $values) {
            // save into the right table
            $table = '#__zoo_zl_search_' . $dataType;

            /** @var \JDatabaseQuery $query */
            $query = $db->getQuery(true);

            $dataValues = [];
            foreach ($values as $element_id => $vs) {
                foreach ($vs as $v) {
                    $dataValues[] = implode(",", [$db->q($item->id), $db->q($element_id), $db->q($v)]);
                }
            }

            $query->insert($table)->columns(['item_id', 'element_id', 'value'])->values($dataValues);
            $db->setQuery($query);
            $db->execute();
        }
    }

    /**
     * @param \Item $item
     * @param array|null $dataTypes
     */
    public function cleanSearchIndex(\Item $item, $dataTypes = null)
    {
        $db = $this->container->db;

        if (!$dataTypes) {
            $dataTypes = \Zoolanders\Framework\Element\Indexer::getAvailableDataTypes();
        }

        foreach ($dataTypes as $dataType) {

            $table = '#__zoo_zl_search_' . $dataType;

            /** @var \JDatabaseQuery $query */
            $query = $db->getQuery(true);
            $query->delete()->from($table)->where('item_id = ' . (int)$item->id);
            $db->setQuery($query);
            $db->execute();
        }
    }
}