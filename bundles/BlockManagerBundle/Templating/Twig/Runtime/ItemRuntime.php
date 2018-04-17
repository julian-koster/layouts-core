<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime;

use Exception;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ItemRuntime
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    /**
     * @var \Netgen\BlockManager\Item\UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug = false;

    public function __construct(
        ItemLoaderInterface $itemLoader,
        UrlGeneratorInterface $urlGenerator,
        LoggerInterface $logger = null
    ) {
        $this->itemLoader = $itemLoader;
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Sets if debug is enabled or not.
     *
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (bool) $debug;
    }

    /**
     * The method returns the full path of provided item.
     *
     * It accepts three kinds of references to the item:
     *
     * 1) URI with value_type://value format, e.g. type://42
     * 2) ID and value type as separate arguments
     * 3) \Netgen\BlockManager\Item\ItemInterface object
     *
     * @param int|string|\Netgen\BlockManager\Item\ItemInterface $value
     * @param string|null $valueType
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If provided item or item reference is not valid
     *
     * @return string
     */
    public function getItemPath($value, $valueType = null)
    {
        try {
            $item = null;

            if (is_string($value) && $valueType === null) {
                $itemUri = parse_url($value);
                if (!is_array($itemUri) || empty($itemUri['scheme']) || (empty($itemUri['host']) && $itemUri['host'] !== '0')) {
                    throw ItemException::invalidValue($value);
                }

                $item = $this->itemLoader->load(
                    $itemUri['host'],
                    str_replace('-', '_', $itemUri['scheme'])
                );
            } elseif (is_scalar($value) && is_string($valueType)) {
                $item = $this->itemLoader->load($value, $valueType);
            } elseif ($value instanceof ItemInterface) {
                $item = $value;
            }

            if (!$item instanceof ItemInterface) {
                throw ItemException::canNotLoadItem();
            }

            $itemPath = $this->urlGenerator->generate($item);
            if ($itemPath === null) {
                return '';
            }

            return $itemPath;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            if ($this->debug) {
                throw $e;
            }

            return '';
        }
    }
}
