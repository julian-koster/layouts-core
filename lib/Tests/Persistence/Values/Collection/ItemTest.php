<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDefaultProperties()
    {
        $item = new Item();

        self::assertNull($item->id);
        self::assertNull($item->collectionId);
        self::assertNull($item->position);
        self::assertNull($item->type);
        self::assertNull($item->valueId);
        self::assertNull($item->valueType);
        self::assertNull($item->status);
    }

    public function testSetProperties()
    {
        $item = new Item(
            array(
                'id' => 42,
                'collectionId' => 30,
                'position' => 3,
                'type' => APIItem::TYPE_OVERRIDE,
                'valueId' => 32,
                'valueType' => 'ezcontent',
                'status' => Collection::STATUS_PUBLISHED,
            )
        );

        self::assertEquals(42, $item->id);
        self::assertEquals(30, $item->collectionId);
        self::assertEquals(3, $item->position);
        self::assertEquals(APIItem::TYPE_OVERRIDE, $item->type);
        self::assertEquals(32, $item->valueId);
        self::assertEquals('ezcontent', $item->valueType);
        self::assertEquals(Collection::STATUS_PUBLISHED, $item->status);
    }
}
