<?php

namespace ZFTests\Tree;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Tree\Item;

/**
 * Class ItemTest
 * Tree structures
 *
 * @package ZFTests\Tree
 */
class ItemTest extends ZFTestCase
{
    /**
     * Test setParent / getParent
     *
     * @covers      Item::getID()
     * @covers      Item::setParent()
     * @covers      Item::getParent()
     */
    public function testGetSetParent(){
        $item = new Item();
        $parent_item = new Item();
        $parent_id = $parent_item->getID();

        $item->setParent($parent_item);
        $parent = $item->getParent();

        $this->assertEquals($parent_id, $parent->getID());
        //$this->assertTrue($parent->hasChild($item->getID()));
    }

    /**
     * Test getChildren / setChildren hesChild ops
     *
     * @depends     testGetSetParent
     * @covers      Item::addChild
     * @covers      Item::hasChild
     */
    public function testSetHasChild(){
        $item = new Item();
        $child_item = new Item();
        $child_id = $child_item->getID();

        $item->addChild($child_item);
        $this->assertTrue($item->hasChild($child_id));
        $this->assertEquals($item->getID(), $child_item->getParent()->getID());
    }

    /**
     * Test mass children assignment
     *
     * @covers          Item::addChildren
     * @covers          Item::getChildren
     * @covers          Item::hasChildren
     * @dataProvider    itemDataset
     */
    public function testGetSetChildren($itemset){
        $item = new Item();
        $itemId = $item->getID();

        $this->assertEquals(0, $item->hasChildren());

        $item->addChildren($itemset);

        $this->assertEquals(count($itemset), $item->hasChildren());

        foreach($item->getChildren() as $child){
            $this->assertEquals($itemId, $child->getParent()->getID());
            $this->assertTrue($item->hasChild($child->getID()));
        }
    }

    /**
     * Test removing child nodes from an item
     *
     * @depends         testGetSetChildren
     * @covers          Item::removeChild()
     * @dataProvider    itemDataset
     */
    public function testRemovingChildren($itemset){
        $item = new Item();
        $item->addChildren($itemset);
        $children = $item->getChildren();
        $count = $item->hasChildren();

        foreach($children as $child){
            $child_id = $child->getID();
            $this->assertTrue($item->hasChild($child_id));
            $item->removeChild($child);
            $this->assertFalse($item->hasChild($child_id));
            $this->assertEquals(--$count, $item->hasChildren());
        }
    }

    /**
     * Test removing child nodes from an item by it's id
     *
     * @depends         testGetSetChildren
     * @covers          Item::removeChildById()
     * @dataProvider    itemDataset
     */
    public function testRemovingChildrenById($itemset){
        $item = new Item();
        $item->addChildren($itemset);
        $children = $item->getChildren();
        $count = $item->hasChildren();

        foreach($children as $child){
            $child_id = $child->getID();
            $this->assertTrue($item->hasChild($child_id));
            $item->removeChildById($child_id);
            $this->assertFalse($item->hasChild($child_id));
            $this->assertEquals(--$count, $item->hasChildren());
        }
    }

    /**
     * Item dataset for testing mass attachment
     */
    public function itemDataset(){

        return [
            [ [
                new Item(),
                new Item(),
                new Item()
            ] ]
        ];
    }
}
