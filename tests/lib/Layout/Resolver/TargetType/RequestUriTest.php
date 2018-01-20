<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class RequestUriTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri
     */
    private $targetType;

    public function setUp()
    {
        $this->targetType = new RequestUri();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::getType
     */
    public function testGetType()
    {
        $this->assertEquals('request_uri', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::provideValue
     */
    public function testProvideValue()
    {
        $request = Request::create('/the/answer', 'GET', array('a' => 42));

        $this->assertEquals(
            '/the/answer?a=42',
            $this->targetType->provideValue($request)
        );
    }

    /**
     * Provider for testing target type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('/some/route?id=42', true),
            array('/', true),
            array('', false),
            array(null, false),
        );
    }
}
