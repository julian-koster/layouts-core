<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\ValueObject;

/**
 * A result is a wrapper around the item generated by running the collection.
 * Instances of this object are wrapped into a result set, to be used by the block.
 */
final class Result extends ValueObject
{
    /**
     * Defines a result generated from the manual collection item.
     */
    const TYPE_MANUAL = 0;

    /**
     * Defines a result generated from the override collection item.
     */
    const TYPE_OVERRIDE = 1;

    /**
     * Defines a result generated from the item coming from the collection query.
     */
    const TYPE_DYNAMIC = 2;

    /**
     * @var \Netgen\BlockManager\Item\ItemInterface
     */
    protected $item;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Item
     */
    protected $collectionItem;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int
     */
    protected $position;

    /**
     * Returns the final generated item.
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Returns the collection item which was used to generate the final result.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getCollectionItem()
    {
        return $this->collectionItem;
    }

    /**
     * Returns the type of the result. It can be one of self::TYPE_* constants.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the position of the result within the set.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
