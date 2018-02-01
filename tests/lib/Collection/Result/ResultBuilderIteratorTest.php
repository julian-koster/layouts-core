<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use ArrayIterator;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultBuilderIterator;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ResultBuilderIteratorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemLoaderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemBuilderMock;

    public function setUp()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilderIterator::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilderIterator::current
     */
    public function testCurrent()
    {
        $items = $this->getItems();

        $this->itemBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($this->equalTo(new stdClass()))
            ->will(
                $this->returnValue(
                    new Item(
                        array(
                            'value' => 100,
                            'valueType' => 'dynamicValue',
                        )
                    )
                )
            );

        $this->itemLoaderMock
            ->expects($this->at(0))
            ->method('load')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will(
                $this->returnValue(
                    new Item(
                        array(
                            'value' => 42,
                            'valueType' => 'value',
                        )
                    )
                )
            );

        $this->itemLoaderMock
            ->expects($this->at(1))
            ->method('load')
            ->with($this->equalTo(999), $this->equalTo('value'))
            ->will($this->throwException(new ItemException()));

        $iterator = new ResultBuilderIterator(
            new ArrayIterator($items),
            $this->itemLoaderMock,
            $this->itemBuilderMock
        );

        $this->assertEquals(
            array(
                new Result(
                    array(
                        'item' => new Item(
                            array(
                                'value' => 100,
                                'valueType' => 'dynamicValue',
                            )
                        ),
                        'collectionItem' => null,
                        'type' => Result::TYPE_DYNAMIC,
                        'position' => 0,
                    )
                ),
                new Result(
                    array(
                        'item' => new Item(
                            array(
                                'value' => 42,
                                'valueType' => 'value',
                            )
                        ),
                        'collectionItem' => new CollectionItem(
                            array(
                                'value' => 42,
                                'valueType' => 'value',
                            )
                        ),
                        'type' => Result::TYPE_MANUAL,
                        'position' => 1,
                    )
                ),
                new Result(
                    array(
                        'item' => new NullItem(
                            array(
                                'value' => 999,
                            )
                        ),
                        'collectionItem' => new CollectionItem(
                            array(
                                'value' => 999,
                                'valueType' => 'value',
                            )
                        ),
                        'type' => Result::TYPE_MANUAL,
                        'position' => 2,
                    )
                ),
            ),
            iterator_to_array($iterator)
        );
    }

    /**
     * @return mixed[]
     */
    private function getItems()
    {
        return array(
            new stdClass(),
            new CollectionItem(
                array(
                    'value' => 42,
                    'valueType' => 'value',
                )
            ),
            new CollectionItem(
                array(
                    'value' => 999,
                    'valueType' => 'value',
                )
            ),
        );
    }
}
