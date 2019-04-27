<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Serializer\Normalizer;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Serializer\Normalizer\ValueNormalizer;
use Netgen\Layouts\Serializer\Values\Value;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Tests\API\Stubs\Value as StubValue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ValueNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $normalizerMock;

    /**
     * @var \Netgen\Layouts\Serializer\Normalizer\ValueNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new ValueNormalizer();
        $this->normalizer->setNormalizer($this->normalizerMock);
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\ValueNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $value = new StubValue();
        $this->normalizerMock
            ->expects(self::at(0))
            ->method('normalize')
            ->with(
                self::identicalTo($value),
                self::identicalTo('json'),
                self::identicalTo(['context'])
            )
            ->willReturn(['serialized']);

        $data = $this->normalizer->normalize(new Value($value), 'json', ['context']);

        self::assertSame(['serialized'], $data);
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\Layouts\Serializer\Normalizer\ValueNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public function supportsNormalizationProvider(): array
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new StubValue(), false],
            [new Block(), false],
            [new VersionedValue(new Block(), 1), false],
            [new Value([new Block()], 1), true],
        ];
    }
}
