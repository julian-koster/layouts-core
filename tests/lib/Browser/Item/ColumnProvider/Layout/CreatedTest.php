<?php

namespace Netgen\BlockManager\Tests\Browser\Item\ColumnProvider\Layout;

use DateTime;
use Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Created;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use PHPUnit\Framework\TestCase;

final class CreatedTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Created
     */
    private $provider;

    public function setUp()
    {
        $this->provider = new Created('d.m.Y H:i:s');
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Created::__construct
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Created::getValue
     */
    public function testGetValue()
    {
        $date = new DateTime();
        $date->setDate(2016, 7, 17);
        $date->setTime(18, 15, 42);

        $item = new Item(
            new Layout(
                array(
                    'created' => $date,
                )
            )
        );

        $this->assertEquals(
            '17.07.2016 18:15:42',
            $this->provider->getValue($item)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Created::getValue
     */
    public function testGetValueWithInvalidItem()
    {
        $this->assertNull($this->provider->getValue(new StubItem()));
    }
}
