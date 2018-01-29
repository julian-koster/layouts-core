<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache;

use Netgen\BlockManager\View\CacheableViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CacheableViewListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => 'onView',
            KernelEvents::RESPONSE => array('onKernelResponse', -255),
        );
    }

    /**
     * Adds the caching headers for the view provided by the event.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $controllerResult = $event->getControllerResult();
        if (!$controllerResult instanceof ViewInterface) {
            return;
        }

        $event->getRequest()->attributes->set('ngbmView', $controllerResult);
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $view = $event->getRequest()->attributes->get('ngbmView');
        if (!$view instanceof CacheableViewInterface) {
            return;
        }

        $this->setUpCachingHeaders($view, $event->getResponse());
    }

    /**
     * @param \Netgen\BlockManager\View\CacheableViewInterface $cacheableView
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    private function setUpCachingHeaders(CacheableViewInterface $cacheableView, Response $response)
    {
        if (!$cacheableView->isCacheable()) {
            return;
        }

        if (!$response->headers->hasCacheControlDirective('s-maxage')) {
            $sharedMaxAge = (int) $cacheableView->getSharedMaxAge();
            $response->setSharedMaxAge($sharedMaxAge);
        }
    }
}
