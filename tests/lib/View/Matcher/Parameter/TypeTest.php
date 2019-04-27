<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Parameter;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\TextType;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Parameter\Type;
use Netgen\Layouts\View\View\ParameterView;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\View\Matcher\MatcherInterface
     */
    private $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Type();
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Parameter\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $parameter = Parameter::fromArray(
            [
                'parameterDefinition' => ParameterDefinition::fromArray(
                    [
                        'type' => new TextType(),
                    ]
                ),
            ]
        );

        $view = new ParameterView($parameter);

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['boolean'], false],
            [['text'], true],
            [['boolean', 'integer'], false],
            [['boolean', 'text'], true],
        ];
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Parameter\Type::match
     */
    public function testMatchWithNoParameterView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
