<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\View\Provider\LayoutViewProvider;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\API\Stubs\Value;

class LayoutViewProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\Provider\LayoutViewProvider::provideView
     */
    public function testProvideView()
    {
        $layout = new Layout(array('id' => 42));

        $layoutViewProvider = new LayoutViewProvider();

        /** @var \Netgen\BlockManager\View\LayoutViewInterface $view */
        $view = $layoutViewProvider->provideView($layout);

        self::assertInstanceOf('Netgen\BlockManager\View\LayoutViewInterface', $view);

        self::assertEquals($layout, $view->getLayout());
        self::assertEquals(null, $view->getTemplate());
        self::assertEquals(
            array(
                'layout' => $layout,
            ),
            $view->getParameters()
        );
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\LayoutViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        $layoutViewProvider = new LayoutViewProvider();
        self::assertEquals($supports, $layoutViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new Value(), false),
            array(new Block(), false),
            array(new Layout(), true),
        );
    }
}
