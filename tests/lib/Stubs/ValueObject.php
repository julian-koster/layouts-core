<?php

namespace Netgen\BlockManager\Tests\Stubs;

use Netgen\BlockManager\ValueObject as BaseValueObject;

final class ValueObject extends BaseValueOBject
{
    /**
     * @var int
     */
    public $status;

    /**
     * @var mixed
     */
    public $someProperty;

    /**
     * @var mixed
     */
    public $someOtherProperty;
}
