<?php

namespace Netgen\BlockManager\Traits\Tests;

use Netgen\BlockManager\Traits\Tests\Stubs\RequestStackAwareValue;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestStackAwareTraitTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultRequestStackValue()
    {
        $value = new RequestStackAwareValue();
        self::assertNull($value->getRequestStack());
    }

    /**
     * @covers \Netgen\BlockManager\Traits\RequestStackAwareTrait::setRequestStack
     */
    public function testSetRequestStack()
    {
        $requestStack = new RequestStack();

        $value = new RequestStackAwareValue();
        $value->setRequestStack($requestStack);

        self::assertEquals($requestStack, $value->getRequestStack());
    }
}
