<?php

namespace Netgen\BlockManager\Parameters\Registry;

use Netgen\BlockManager\Parameters\Form\MapperInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class FormMapperRegistry implements FormMapperRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\MapperInterface[]
     */
    protected $formMappers = array();

    /**
     * Adds a parameter form mapper to registry.
     *
     * @param string $parameterType
     * @param \Netgen\BlockManager\Parameters\Form\MapperInterface $formMapper
     */
    public function addFormMapper($parameterType, MapperInterface $formMapper)
    {
        $this->formMappers[$parameterType] = $formMapper;
    }

    /**
     * Returns if registry has a parameter form mapper.
     *
     * @param string $parameterType
     *
     * @return bool
     */
    public function hasFormMapper($parameterType)
    {
        return isset($this->formMappers[$parameterType]);
    }

    /**
     * Returns a form mapper for provided parameter type.
     *
     * @param string $parameterType
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If form mapper does not exist
     *
     * @return \Netgen\BlockManager\Parameters\Form\MapperInterface
     */
    public function getFormMapper($parameterType)
    {
        if (!$this->hasFormMapper($parameterType)) {
            throw new InvalidArgumentException(
                'type',
                sprintf(
                    'Form mapper for "%s" parameter type does not exist.',
                    $parameterType
                )
            );
        }

        return $this->formMappers[$parameterType];
    }

    /**
     * Returns all form mappers.
     *
     * @return \Netgen\BlockManager\Parameters\Form\MapperInterface[]
     */
    public function getFormMappers()
    {
        return $this->formMappers;
    }
}
