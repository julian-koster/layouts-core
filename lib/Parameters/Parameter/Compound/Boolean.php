<?php

namespace Netgen\BlockManager\Parameters\Parameter\Compound;

use Netgen\BlockManager\Parameters\CompoundParameter;
use Symfony\Component\Validator\Constraints;

class Boolean extends CompoundParameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'compound_boolean';
    }

    /**
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->isRequired && $this->defaultValue === null) {
            return false;
        }

        return parent::getDefaultValue();
    }

    /**
     * Returns constraints that are common to all parameters.
     *
     * Overriden because base NotBlank constraint checks for `false` too.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getBaseConstraints()
    {
        if ($this->isRequired()) {
            return array(
                new Constraints\NotNull(),
            );
        }

        return array();
    }

    /**
     * Returns constraints that are specific to parameter.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getParameterConstraints()
    {
        return array(
            new Constraints\Type(
                array(
                    'type' => 'bool',
                )
            ),
        );
    }
}
