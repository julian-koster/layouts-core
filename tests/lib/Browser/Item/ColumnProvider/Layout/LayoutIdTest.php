<?php

namespace Netgen\BlockManager\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\LayoutId;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use PHPUnit\Framework\TestCase;

final class LayoutIdTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\LayoutId
     */
    private $provider;

    public function setUp()
    {
        $this->provider = new LayoutId();
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\LayoutId::getValue
     */
    public function testGetValue()
    {
        $item = new Item(
            new Layout(
                array(
                    'id' => 42,
                )
            )
        );

        $this->assertEquals(
            42,
            $this->provider->getValue($item)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\LayoutId::getValue
     */
    public function testGetValueWithInvalidItem()
    {
        $this->assertNull($this->provider->getValue(new StubItem()));
    }
}
