<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\ParameterStruct;

class QueryUpdateStruct extends ParameterStruct
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public $queryType;
}
