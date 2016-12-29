<?php

namespace ZFTests\Model;

use Zoolanders\Framework\Model\Item;

/**
 * Class ItemTest
 * Item test
 *
 * @package ZFTests\Model
 */
class ItemTest extends DatabaseTest
{
    /**
     * Creates and returns instance of testing class
     */
    protected function getTestInstance(){

        return new Item(self::$container);
    }

    /**
     * Fieldset provider
     */
    public function fieldsetProvider(){
        return [
            [ ['id'], "SELECT `a`.*,`a`.`id`FROM `#__zoo_item` AS `a`" ],
            [ ['id','alias'], "SELECT `a`.*,`a`.`id`,`a`.`alias`FROM `#__zoo_item` AS `a`" ]
        ];
    }

    /**
     * Where clause testing dataset
     */
    public function whereClauseProvider()
    {
        return [
            [ ['name' => 'id', 'operator' => '=', 'value' => 1], 'SELECT `a`.*FROM `#__zoo_item` AS `a`WHERE `id` = \'1\'']
        ];
    }
}
