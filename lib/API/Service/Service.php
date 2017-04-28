<?php

namespace Netgen\BlockManager\API\Service;

interface Service
{
    /**
     * Begins a transaction.
     */
    public function beginTransaction();

    /**
     * Commits the transaction.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If no transaction has been started
     */
    public function commitTransaction();

    /**
     * Rollbacks the transaction.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If no transaction has been started
     */
    public function rollbackTransaction();
}
