<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class LayoutResolverMapperTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper
     */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new LayoutResolverMapper();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapRules
     */
    public function testMapRules(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'layout_id' => '24',
                'enabled' => '1',
                'priority' => '2',
                'comment' => 'Comment',
                'status' => '1',
                'layout_uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
            ],
            [
                'id' => '43',
                'uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'layout_id' => '25',
                'enabled' => '0',
                'priority' => '3',
                'comment' => null,
                'status' => Value::STATUS_DRAFT,
                'layout_uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'layoutId' => 24,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'enabled' => true,
                'priority' => 2,
                'comment' => 'Comment',
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 43,
                'uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'layoutId' => 25,
                'layoutUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'enabled' => false,
                'priority' => 3,
                'comment' => null,
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $rules = $this->mapper->mapRules($data);

        self::assertContainsOnlyInstancesOf(Rule::class, $rules);
        self::assertSame($expectedData, $this->exportObjectList($rules));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapTargets
     */
    public function testMapTargets(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'rule_id' => '1',
                'rule_uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'type' => 'target',
                'value' => '32',
                'status' => '1',
            ],
            [
                'id' => 43,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'rule_id' => 2,
                'rule_uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'type' => 'target2',
                'value' => '42',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'ruleId' => 1,
                'ruleUuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'type' => 'target',
                'value' => '32',
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 43,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'ruleId' => 2,
                'ruleUuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'type' => 'target2',
                'value' => '42',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $targets = $this->mapper->mapTargets($data);

        self::assertContainsOnlyInstancesOf(Target::class, $targets);
        self::assertSame($expectedData, $this->exportObjectList($targets));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapConditions
     */
    public function testMapConditions(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'rule_id' => '1',
                'rule_uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'type' => 'condition',
                'value' => '24',
                'status' => '1',
            ],
            [
                'id' => 43,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'rule_id' => 2,
                'rule_uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'type' => 'condition2',
                'value' => '{"param":"value"}',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'ruleId' => 1,
                'ruleUuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'type' => 'condition',
                'value' => 24,
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 43,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'ruleId' => 2,
                'ruleUuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'type' => 'condition2',
                'value' => [
                    'param' => 'value',
                ],
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $conditions = $this->mapper->mapConditions($data);

        self::assertContainsOnlyInstancesOf(Condition::class, $conditions);
        self::assertSame($expectedData, $this->exportObjectList($conditions));
    }
}
