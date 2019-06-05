<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache;

use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCache\Exception\ExceptionCollection;
use Netgen\Layouts\HttpCache\Varnish\HostHeaderProviderInterface;

final class VarnishClient implements ClientInterface
{
    /**
     * @var \FOS\HttpCache\CacheInvalidator
     */
    private $fosInvalidator;

    /**
     * @var \Netgen\Layouts\HttpCache\Varnish\HostHeaderProviderInterface
     */
    private $hostHeaderProvider;

    public function __construct(
        CacheInvalidator $fosInvalidator,
        HostHeaderProviderInterface $hostHeaderProvider
    ) {
        $this->fosInvalidator = $fosInvalidator;
        $this->hostHeaderProvider = $hostHeaderProvider;
    }

    public function purge(array $tags): void
    {
        $hostHeader = $this->hostHeaderProvider->provideHostHeader();

        foreach ($tags as $tag) {
            $this->fosInvalidator->invalidatePath(
                '/',
                [
                    'key' => $tag,
                    'Host' => $hostHeader,
                ]
            );
        }
    }

    public function commit(): bool
    {
        try {
            $this->fosInvalidator->flush();
        } catch (ExceptionCollection $e) {
            // Do nothing, FOS invalidator will write to log.
            return false;
        }

        return true;
    }
}
