<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values;

use Netgen\Layouts\Tests\API\Stubs\ValueWithLazyProperty;
use PHPUnit\Framework\TestCase;

final class LazyPropertyTraitTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Tests\API\Stubs\ValueWithLazyProperty
     */
    private $value;

    protected function setUp(): void
    {
        $this->value = new ValueWithLazyProperty(
            static function (): int {
                return 42;
            }
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LazyPropertyTrait::getLazyProperty
     */
    public function testGetLazyProperty(): void
    {
        self::assertIsCallable($this->value->value);

        self::assertSame(42, $this->value->getValue());
        self::assertSame(42, $this->value->value);
    }
}
