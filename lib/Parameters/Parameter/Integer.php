<?php

namespace Netgen\BlockManager\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class Integer extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'integer';
    }

    /**
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->isRequired && $this->defaultValue === null) {
            return $this->options['min'];
        }

        return parent::getDefaultValue();
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('min', null);
        $optionsResolver->setDefault('max', null);

        $optionsResolver->setRequired(array('min', 'max'));

        $optionsResolver->setAllowedTypes('min', array('int', 'null'));
        $optionsResolver->setAllowedTypes('max', array('int', 'null'));

        $optionsResolver->setNormalizer(
            'max',
            function (Options $options, $value) {
                if ($value === null || $options['min'] === null) {
                    return $value;
                }

                if ($value < $options['min']) {
                    return $options['min'];
                }

                return $value;
            }
        );
    }

    /**
     * Returns constraints that are specific to parameter.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getParameterConstraints()
    {
        $constraints = array(
            new Constraints\Type(
                array(
                    'type' => 'int',
                )
            ),
        );

        if ($this->options['min'] !== null) {
            $constraints[] = new Constraints\GreaterThanOrEqual(
                array('value' => $this->options['min'])
            );
        }

        if ($this->options['max'] !== null) {
            $constraints[] = new Constraints\LessThanOrEqual(
                array('value' => $this->options['max'])
            );
        }

        return $constraints;
    }
}
