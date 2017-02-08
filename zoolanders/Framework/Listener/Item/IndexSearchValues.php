<?php

namespace Zoolanders\Framework\Listener\Item;

use Zoolanders\Framework\Event\Item;
use Zoolanders\Framework\Listener\Listener;

class IndexSearchValues extends Listener
{
    /**
     * @param Item\Saved $event
     */
    public function handle(Item\Saved $event)
    {
        $item = $event->getItem();

        $dataToSave = [];

        /** @var \Element $element **/
        foreach ($item->getElements() as $element) {
            $dataType = $this->getDataTypeFromElement($element);
            $values = $this->getValuesFromElement($element);

            $dataToSave[$dataType][$element->identifier] = $values;
        }

        foreach ($dataToSave as $dataType => $values) {
            // save into the right table
            $table = '#__zoo_zl_search_' . $dataType;

            /** @var \JDatabaseQuery $query */
            $query = $this->container->db->getQuery(true);
            $query->delete()->from($table)->where('item_id = ' . (int) $item->id);
            $this->container->db->setQuery($query);
            $this->container->db->execute();

            /** @var \JDatabaseQuery $query */
            $query = $this->container->db->getQuery(true);
            $db = $this->container->db;

            $dataValues = [];
            foreach ($values as $element_id => $vs) {
                foreach ($vs as $v) {
                    $dataValues[] = implode(",", [$db->q($item->id), $db->q($element_id), $db->q($v)]);
                }
            }

            $query->insert($table)->columns(['item_id', 'element_id', 'value'])->values($dataValues);
            $this->container->db->setQuery($query);
            $this->container->db->execute();
        }

    }

    /**
     * @param $element
     * @return array
     */
    protected function getValuesFromElement($element)
    {
        $values = [];

        /*if ($element instanceof \ElementRepeatable) {

            foreach ($element as $el) {
                $subValues = $this->getValuesFromElement($el);

                foreach ($subValues as $v) {
                    $values[] = $v;
                }
            }

            return $values;
        }*/

        $values[] =  $element->getSearchData();

        return $values;
    }

    /**
     * @param $element
     * @return string
     */
    protected function getDataTypeFromElement($element)
    {
        $type = $element->getElementType();

        switch ($type) {
            case 'text':
            case 'textpro':
                return 'string';
                break;
            case 'date':
            case 'datepro':
                return 'datetime';
                break;
            case 'textareapro':
            case 'textarea':
            default:
                return 'text';
                break;
        }
    }
}