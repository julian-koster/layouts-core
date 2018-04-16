<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use DateTimeImmutable;
use DateTimeZone;
use Netgen\BlockManager\Parameters\ParameterType\DateTimeType;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class DateTimeTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    public function setUp()
    {
        $this->type = new DateTimeType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('datetime', $this->type->getIdentifier());
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, $isEmpty)
    {
        $this->assertEquals($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     *
     * @return array
     */
    public function emptyProvider()
    {
        return array(
            array(null, true),
            array(new DateTimeImmutable(), false),
            array(new DateTimeImmutable(), false),
            array(new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), false),
            array(new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), false),
        );
    }

    /**
     * @param mixed $value
     * @param bool $convertedValue
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::toHash
     * @dataProvider toHashProvider
     */
    public function testToHash($value, $convertedValue)
    {
        $this->assertEquals($convertedValue, $this->type->toHash($this->getParameterDefinition(), $value));
    }

    public function toHashProvider()
    {
        return array(
            array(42, null),
            array(null, null),
            array(array(), null),
            array(array('datetime' => '2018-02-01 00:00:00'), null),
            array(array('timezone' => 'Antarctica/Casey'), null),
            array(array('datetime' => '2018-02-01 00:00:00', 'timezone' => ''), null),
            array(array('datetime' => '', 'timezone' => 'Antarctica/Casey'), null),
            array(array('datetime' => '', 'timezone' => ''), null),
            array(array('datetime' => '2018-02-01 15:00:00', 'timezone' => 'Antarctica/Casey'), array('datetime' => '2018-02-01 15:00:00.000000', 'timezone' => 'Antarctica/Casey')),
            array(new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), array('datetime' => '2018-02-01 15:00:00.000000', 'timezone' => 'Antarctica/Casey')),
            array(new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), array('datetime' => '2018-02-01 15:00:00.000000', 'timezone' => 'Antarctica/Casey')),
        );
    }

    /**
     * @param mixed $value
     * @param bool $convertedValue
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::fromHash
     * @dataProvider fromHashProvider
     */
    public function testFromHash($value, $convertedValue)
    {
        $this->assertEquals($convertedValue, $this->type->fromHash($this->getParameterDefinition(), $value));
    }

    public function fromHashProvider()
    {
        return array(
            array(null, null),
            array(array(), null),
            array(array('datetime' => '2018-02-01 00:00:00'), null),
            array(array('timezone' => 'Antarctica/Casey'), null),
            array(array('datetime' => '2018-02-01 00:00:00', 'timezone' => ''), null),
            array(array('datetime' => '', 'timezone' => 'Antarctica/Casey'), null),
            array(array('datetime' => '', 'timezone' => ''), null),
            array(array('datetime' => '2018-02-01 15:00:00.000000', 'timezone' => 'Antarctica/Casey'), new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey'))),
        );
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $parameter = $this->getParameterDefinition();
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(null, true),
            array(new DateTimeImmutable(), true),
            array(new DateTimeImmutable(), true),
            array(new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), true),
            array(new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('Antarctica/Casey')), true),
            array(new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('+01:00')), false),
            array(new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('+01:00')), false),
            array(new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('CAST')), false),
            array(new DateTimeImmutable('2018-02-01 15:00:00', new DateTimeZone('CAST')), false),
        );
    }
}
