<?php

namespace Netgen\Bundle\BlockManagerBundle\Configuration;

interface ConfigurationInterface
{
    const PARAMETER_NAMESPACE = 'netgen_block_manager';

    /**
     * Returns if parameter exists in configuration.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName);

    /**
     * Returns the parameter from configuration.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException If parameter is undefined
     *
     * @return mixed
     */
    public function getParameter($parameterName);
}
