<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\Value;

final class RuleCreateStruct extends Value
{
    /**
     * The ID of the layout to which the rule will be mapped.
     *
     * @var int|string|null
     */
    public $layoutId;

    /**
     * Priority of the rule.
     *
     * @var int|null
     */
    public $priority;

    /**
     * Specifies if the rule will be enabled or not.
     *
     * @var bool
     */
    public $enabled = false;

    /**
     * Description of the rule.
     *
     * @var string|null
     */
    public $comment;
}
